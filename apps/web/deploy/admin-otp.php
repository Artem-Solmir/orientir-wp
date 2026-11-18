<?php
// Sends a one-time code for /admin login (local dev + ENABLE_ADMIN=1). Settings live in private/.
declare(strict_types=1);

ini_set('display_errors', '0');

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $body): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

function b64url(string $bin): string
{
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

$method = $_SERVER['REQUEST_METHOD'] ?? '';
if ($method === 'OPTIONS') {
    header('Allow: POST, OPTIONS');
    http_response_code(204);
    exit;
}
if ($method !== 'POST') {
    header('Allow: POST, OPTIONS');
    respond(405, ['error' => 'Method not allowed']);
}

$configFile = null;
foreach (
    [
        dirname(__DIR__) . '/private/orientir-admin-otp-config.php',
        __DIR__ . '/private/orientir-admin-otp-config.php',
    ] as $path
) {
    if (is_file($path)) {
        $configFile = $path;
        break;
    }
}
if ($configFile === null) {
    respond(500, ['error' => 'Вход по коду не настроен']);
}
/** @var array{email:string,secret:string,from:string,origins:string[]} $config */
$config = require $configFile;
if (strlen($config['secret'] ?? '') < 32) {
    respond(500, ['error' => 'Вход по коду не настроен']);
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
    if (!in_array($origin, $config['origins'], true)) {
        respond(403, ['error' => 'Forbidden']);
    }
}

$action = $_POST['action'] ?? '';
if (!in_array($action, ['request', 'verify'], true)) {
    respond(400, ['error' => 'Некорректный запрос']);
}

function smtp_read($socket): array
{
    $text = '';
    while (($line = fgets($socket, 515)) !== false) {
        $text .= $line;
        if (strlen($line) < 4 || $line[3] !== '-') {
            break;
        }
    }
    return [(int) substr($text, 0, 3), $text];
}

function smtp_cmd($socket, string $command, int $expect): bool
{
    fwrite($socket, $command . "\r\n");
    [$code] = smtp_read($socket);
    return $code === $expect;
}

/** @param array{host:string,port:int,user:string,pass:string} $smtp */
function smtp_send(array $smtp, string $fromAddress, string $to, string $headers, string $body): bool
{
    $socket = @stream_socket_client(
        'ssl://' . $smtp['host'] . ':' . $smtp['port'],
        $errno,
        $errstr,
        15,
    );
    if ($socket === false) {
        return false;
    }
    stream_set_timeout($socket, 15);
    [$greeting] = smtp_read($socket);
    $ok = $greeting === 220
        && smtp_cmd($socket, 'EHLO orientir.by', 250)
        && smtp_cmd($socket, 'AUTH LOGIN', 334)
        && smtp_cmd($socket, base64_encode($smtp['user']), 334)
        && smtp_cmd($socket, base64_encode(str_replace(' ', '', (string) ($smtp['pass'] ?? ''))), 235)
        && smtp_cmd($socket, 'MAIL FROM:<' . $fromAddress . '>', 250)
        && smtp_cmd($socket, 'RCPT TO:<' . $to . '>', 250)
        && smtp_cmd($socket, 'DATA', 354);
    if ($ok) {
        $message = $headers . "\r\n\r\n" . str_replace("\n.", "\n..", $body);
        $ok = smtp_cmd($socket, $message . "\r\n.", 250);
    }
    @fwrite($socket, "QUIT\r\n");
    fclose($socket);
    return $ok;
}

function allowed(string $key, int $max, int $windowSeconds): bool
{
    $file = sys_get_temp_dir() . '/orientir-rl-' . hash('sha256', $key);
    $handle = fopen($file, 'c+');
    if ($handle === false || !flock($handle, LOCK_EX)) {
        return true;
    }
    $now = time();
    $hits = array_filter(
        array_map('intval', explode(',', (string) stream_get_contents($handle))),
        static fn (int $t): bool => $t > $now - $windowSeconds,
    );
    $allowed = count($hits) < $max;
    if ($allowed) {
        $hits[] = $now;
    }
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, implode(',', $hits));
    flock($handle, LOCK_UN);
    fclose($handle);
    return $allowed;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!allowed('admin-otp-ip:' . $ip, 8, 3600)) {
    respond(429, ['error' => 'Слишком много попыток. Попробуйте позже']);
}

$codeDir = sys_get_temp_dir() . '/orientir-admin-otp';
if (!is_dir($codeDir)) {
    mkdir($codeDir, 0700, true);
}

if ($action === 'request') {
    if (!allowed('admin-otp-req:' . $ip, 4, 900)) {
        respond(429, ['error' => 'Код уже недавно отправляли. Подождите несколько минут']);
    }
    $code = (string) random_int(100000, 999999);
    $hash = hash('sha256', $code . '|' . $config['secret']);
    $payload = ['exp' => time() + 600, 'tries' => 0];
    file_put_contents($codeDir . '/' . $hash, json_encode($payload), LOCK_EX);
    $subject = mb_encode_mimeheader('Код входа в админку Ориентир', 'UTF-8');
    $body = "Код для входа: {$code}\n\nДействует 10 минут.\n";
    $fromAddress = preg_match('/<([^>]+)>/', $config['from'], $m) === 1 ? $m[1] : $config['from'];
    $host = parse_url('https://' . ($_SERVER['HTTP_HOST'] ?? 'orientir.by'), PHP_URL_HOST) ?: 'orientir.by';
    $headers = [
        'From: ' . $config['from'],
        'Reply-To: ' . $fromAddress,
        'Return-Path: ' . $fromAddress,
        'Date: ' . date('r'),
        'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $host . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    $smtp = $config['smtp'] ?? null;
    if (is_array($smtp)) {
        $fromAddress = $smtp['user'];
        $from = 'From: ' . mb_encode_mimeheader('Сайт Ориентир', 'UTF-8') . ' <' . $fromAddress . '>';
        $headers[0] = $from;
        $headers[1] = 'Reply-To: ' . $fromAddress;
        $headers[2] = 'To: ' . $config['email'];
        $headers[] = 'Subject: ' . $subject;
        $sent = smtp_send($smtp, $fromAddress, $config['email'], implode("\r\n", $headers), $body);
    } else {
        $sent = mail($config['email'], $subject, $body, implode("\r\n", $headers), '-f' . $fromAddress);
    }
    if (!$sent) {
        respond(500, ['error' => 'Не удалось отправить письмо']);
    }
    respond(200, ['ok' => true, 'hint' => $config['email']]);
}

$codeRaw = trim((string) ($_POST['code'] ?? ''));
if (!preg_match('/^\d{6}$/', $codeRaw)) {
    respond(422, ['error' => 'Введите шестизначный код из письма']);
}

$hash = hash('sha256', $codeRaw . '|' . $config['secret']);
$file = $codeDir . '/' . $hash;
if (!is_file($file)) {
    respond(422, ['error' => 'Неверный или устаревший код']);
}
/** @var array{exp:int,tries:int} $stored */
$stored = json_decode((string) file_get_contents($file), true);
if (!is_array($stored) || ($stored['exp'] ?? 0) < time() || ($stored['tries'] ?? 0) >= 8) {
    @unlink($file);
    respond(422, ['error' => 'Неверный или устаревший код']);
}

@unlink($file);

require_once __DIR__ . '/admin-session.php';
$session = admin_make_session_token($config['secret']);
admin_set_session_cookie($session);
$payload = ['ok' => true, 'email' => $config['email']];
$localOrigins = ['http://localhost:3000', 'http://127.0.0.1:3000'];
if ($origin !== '' && in_array($origin, $localOrigins, true)) {
  $expMs = (int) round(microtime(true) * 1000) + 120_000;
  $body = 'admin-otp:' . $expMs;
  $sig = hash_hmac('sha256', $body, $config['secret'], true);
  $payload['token'] = b64url($body) . '.' . b64url($sig);
}
respond(200, $payload);
