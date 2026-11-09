<?php
// Receives the "Прислать материалы" form from the static site and mails it to the club.
// Runs on the existing PHP hosting; it is the ONLY executable PHP file in the docroot (see .htaccess).
declare(strict_types=1);

ini_set('display_errors', '0'); // warnings must never leak into the JSON answer

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(405, ['error' => 'Method not allowed']);
}

$configFile = __DIR__ . '/send-config.php';
if (!is_file($configFile)) {
    respond(500, ['error' => 'Форма не настроена']);
}
/** @var array{to:string,from:string,origins:string[]} $config */
$config = require $configFile;

// Same-origin only: a browser form on another site cannot use this endpoint.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && !in_array($origin, $config['origins'], true)) {
    respond(403, ['error' => 'Forbidden']);
}

$raw = file_get_contents('php://input', false, null, 0, 40001);
if ($raw === false || strlen($raw) > 40000) {
    respond(413, ['error' => 'Слишком большой запрос']);
}
$data = json_decode($raw, true);
if (!is_array($data)) {
    respond(400, ['error' => 'Некорректный запрос']);
}

// Honeypot: real visitors never fill this field. Pretend success to bots.
if (($data['website'] ?? '') !== '') {
    respond(200, ['ok' => true]);
}

function field(array $data, string $key, int $min, int $max, string $message): string
{
    $value = $data[$key] ?? '';
    if (!is_string($value)) {
        respond(422, ['error' => $message]);
    }
    // Drop control characters (keeps newlines in the message only; see below) and trim.
    $value = trim(preg_replace('/[^\P{C}\n]/u', '', $value) ?? '');
    $length = mb_strlen($value);
    if ($length < $min || $length > $max) {
        respond(422, ['error' => $message]);
    }
    return $value;
}

$name = field($data, 'name', 2, 80, 'Укажите имя');
$contact = field($data, 'contact', 3, 120, 'Укажите email или телефон');
$title = field($data, 'title', 3, 200, 'Укажите название похода или мероприятия');
$message = field($data, 'message', 10, 10000, 'Расскажите о походе хотя бы в двух предложениях');

$photos = trim((string) ($data['photosUrl'] ?? ''));
if ($photos !== '') {
    $parts = parse_url($photos);
    $hosts = ['disk.yandex.ru', 'disk.yandex.by', 'disk.yandex.com', 'yadi.sk'];
    $ok = is_array($parts)
        && ($parts['scheme'] ?? '') === 'https'
        && !isset($parts['user'], $parts['pass'])
        && in_array($parts['host'] ?? '', $hosts, true)
        && strlen($photos) <= 500;
    if (!$ok) {
        respond(422, ['error' => 'Нужна публичная ссылка на Яндекс Диск (https://disk.yandex.ru/...)']);
    }
}

// Rate limit: 5 per hour per IP and 100 per day overall, kept in small files in the temp dir.
function allowed(string $key, int $max, int $windowSeconds): bool
{
    $file = sys_get_temp_dir() . '/orientir-rl-' . hash('sha256', $key);
    $handle = fopen($file, 'c+');
    if ($handle === false || !flock($handle, LOCK_EX)) {
        return true; // never block real users because of a housekeeping failure
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
if (!allowed('ip:' . $ip, 5, 3600) || !allowed('all', 100, 86400)) {
    respond(429, ['error' => 'Слишком много отправок. Попробуйте позже']);
}

// Header injection: single-line values only, subject encoded, Reply-To only when it is a plain email.
$oneLine = static fn (string $s): string => preg_replace('/\s+/u', ' ', $s) ?? '';
$subject = mb_encode_mimeheader('Материалы с сайта: ' . $oneLine($title), 'UTF-8');
$headers = [
    'From: ' . $config['from'],
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
];
if (filter_var($contact, FILTER_VALIDATE_EMAIL) !== false) {
    $headers[] = 'Reply-To: ' . $contact;
}

$body = "Имя: {$oneLine($name)}\nКонтакт: {$oneLine($contact)}\nНазвание: {$oneLine($title)}\n"
    . 'Фото: ' . ($photos !== '' ? $photos : 'не указано') . "\n\n" . $message . "\n";

if (!mail($config['to'], $subject, $body, implode("\r\n", $headers))) {
    respond(500, ['error' => 'Не удалось отправить. Попробуйте позже']);
}
respond(200, ['ok' => true]);
