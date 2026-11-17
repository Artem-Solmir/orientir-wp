<?php
declare(strict_types=1);

const ADMIN_SESSION_COOKIE = 'orientir_admin';
const ADMIN_SESSION_TTL = 43200; // 12 h

function admin_session_paths(): array
{
  return [
    dirname(__DIR__) . '/private/orientir-admin-otp-config.php',
    __DIR__ . '/private/orientir-admin-otp-config.php',
  ];
}

/** @return array{email:string,secret:string,from:string,origins:string[]}|null */
function admin_load_config(): ?array
{
  foreach (admin_session_paths() as $path) {
    if (!is_file($path)) {
      continue;
    }
    /** @var mixed $config */
    $config = require $path;
    if (!is_array($config)) {
      continue;
    }
    $secret = (string) ($config['secret'] ?? '');
    if (strlen($secret) < 32) {
      continue;
    }
    return $config;
  }
  return null;
}

function admin_b64url(string $bin): string
{
  return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function admin_b64url_decode(string $s): string|false
{
  $pad = str_repeat('=', (4 - strlen($s) % 4) % 4);
  return base64_decode(strtr($s . $pad, '-_', '+/'), true);
}

function admin_make_session_token(string $secret): string
{
  $exp = time() + ADMIN_SESSION_TTL;
  $body = 'admin:' . $exp;
  $sig = hash_hmac('sha256', $body, $secret, true);
  return $body . '.' . admin_b64url($sig);
}

function admin_verify_session_token(string $token, string $secret): bool
{
  $dot = strrpos($token, '.');
  if ($dot === false) {
    return false;
  }
  $body = substr($token, 0, $dot);
  $sigBin = admin_b64url_decode(substr($token, $dot + 1));
  if ($sigBin === false || !str_starts_with($body, 'admin:')) {
    return false;
  }
  $exp = (int) substr($body, 6);
  if ($exp < time()) {
    return false;
  }
  $expected = hash_hmac('sha256', $body, $secret, true);
  return hash_equals($expected, $sigBin);
}

function admin_set_session_cookie(string $token): void
{
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
  setcookie(ADMIN_SESSION_COOKIE, $token, [
    'expires' => time() + ADMIN_SESSION_TTL,
    'path' => '/',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
}

function admin_clear_session_cookie(): void
{
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
  setcookie(ADMIN_SESSION_COOKIE, '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
}

function admin_require_session(): ?array
{
  $config = admin_load_config();
  if ($config === null) {
    return null;
  }
  $token = $_COOKIE[ADMIN_SESSION_COOKIE] ?? '';
  if ($token === '' || !admin_verify_session_token($token, $config['secret'])) {
    return null;
  }
  return $config;
}
