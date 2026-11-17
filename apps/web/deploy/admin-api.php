<?php
// JSON API for the static admin UI; content lives in private/content/.
declare(strict_types=1);

require_once __DIR__ . '/admin-session.php';

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

$contentRoot = static function (): ?string {
  foreach ([dirname(__DIR__) . '/private/content', __DIR__ . '/private/content'] as $root) {
    if (is_dir($root)) {
      return $root;
    }
  }
  return null;
};

function sanitize_body(string $html): string
{
  $allowed = '<p><br><h2><h3><h4><ul><ol><li><a><strong><em><blockquote><img><figure><figcaption><table><thead><tbody><tr><th><td><iframe>';
  return strip_tags($html, $allowed);
}

function slug_ok(string $slug): bool
{
  return $slug !== '' && strlen($slug) <= 100 && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1;
}

function read_json_file(string $path): mixed
{
  if (!is_file($path)) {
    return null;
  }
  return json_decode((string) file_get_contents($path), true);
}

function write_json_atomic(string $path, array $data): bool
{
  $dir = dirname($path);
  if (!is_dir($dir)) {
    mkdir($dir, 0750, true);
  }
  $tmp = $path . '.' . getmypid() . '.tmp';
  $bytes = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
  if (file_put_contents($tmp, $bytes, LOCK_EX) === false) {
    return false;
  }
  return rename($tmp, $path);
}

$method = $_SERVER['REQUEST_METHOD'] ?? '';
$action = $_POST['action'] ?? ($_GET['action'] ?? '');
$postData = [];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (str_starts_with(strtolower($contentType), 'application/json')) {
  $raw = file_get_contents('php://input', false, null, 0, 500_001);
  if ($raw !== false && strlen($raw) <= 500_000) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
      $postData = $decoded;
      if ($action === '' && isset($decoded['action'])) {
        $action = (string) $decoded['action'];
      }
    }
  }
}
if ($method === 'OPTIONS') {
  header('Allow: GET, POST, OPTIONS');
  http_response_code(204);
  exit;
}

if ($action === '' && $method === 'GET') {
  $action = 'session';
}

if ($action !== 'session' && $method !== 'POST') {
  respond(405, ['error' => 'Method not allowed']);
}

$config = admin_require_session();
if ($config === null && $action !== 'session') {
  respond(401, ['error' => 'Нужно войти']);
}

$root = $contentRoot();
if ($root === null && $action !== 'session' && $action !== 'logout') {
  respond(500, ['error' => 'Контент не настроен на сервере']);
}

if ($action === 'session') {
  if ($config === null) {
    respond(200, ['ok' => false]);
  }
  respond(200, ['ok' => true, 'email' => $config['email']]);
}

if ($action === 'logout') {
  admin_clear_session_cookie();
  respond(200, ['ok' => true]);
}

if ($action === 'list') {
  $postsDir = $root . '/posts';
  $items = [];
  foreach (glob($postsDir . '/*.json') ?: [] as $file) {
    $row = read_json_file($file);
    if (!is_array($row)) {
      continue;
    }
    $items[] = [
      'slug' => (string) ($row['slug'] ?? ''),
      'title' => (string) ($row['title'] ?? ''),
      'status' => (string) ($row['status'] ?? 'published'),
      'publishedAt' => $row['publishedAt'] ?? null,
    ];
  }
  usort($items, static function (array $a, array $b): int {
    return strcmp((string) ($b['publishedAt'] ?? ''), (string) ($a['publishedAt'] ?? ''));
  });
  respond(200, ['ok' => true, 'items' => array_slice($items, 0, 80)]);
}

if ($action === 'categories') {
  $cats = read_json_file($root . '/categories.json');
  if (!is_array($cats)) {
    respond(200, ['ok' => true, 'items' => []]);
  }
  $items = [];
  foreach ($cats as $c) {
    if (is_array($c) && isset($c['slug'], $c['name'])) {
      $items[] = ['slug' => (string) $c['slug'], 'name' => (string) $c['name']];
    }
  }
  respond(200, ['ok' => true, 'items' => $items]);
}

if ($action === 'get') {
  $slug = trim((string) ($_POST['slug'] ?? $_GET['slug'] ?? ''));
  if (!slug_ok($slug)) {
    respond(422, ['error' => 'Некорректный адрес записи']);
  }
  $row = read_json_file($root . '/posts/' . $slug . '.json');
  if (!is_array($row)) {
    respond(404, ['error' => 'Запись не найдена']);
  }
  respond(200, [
    'ok' => true,
    'post' => [
      'slug' => $row['slug'],
      'title' => $row['title'],
      'excerpt' => $row['excerpt'] ?? null,
      'body' => $row['body'] ?? '',
      'authorName' => $row['authorName'] ?? null,
      'categories' => $row['categories'] ?? [],
    ],
  ]);
}

if ($action === 'create') {
  $title = trim((string) ($postData['title'] ?? ''));
  $body = sanitize_body((string) ($postData['body'] ?? ''));
  if (strlen($title) < 3 || $body === '') {
    respond(422, ['error' => 'Заполните заголовок и текст']);
  }
  $slug = trim((string) ($postData['slug'] ?? ''));
  if ($slug === '') {
    $latin = function_exists('transliterator_transliterate')
      ? (transliterator_transliterate('Any-Latin; Latin-ASCII', $title) ?: $title)
      : $title;
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $latin));
    $slug = trim($slug, '-');
  }
  if (!slug_ok($slug)) {
    respond(422, ['error' => 'Укажите корректный slug латиницей']);
  }
  $path = $root . '/posts/' . $slug . '.json';
  if (is_file($path)) {
    respond(422, ['error' => 'Такой slug уже занят']);
  }
  $categories = array_values(array_filter(array_map('strval', $postData['categories'] ?? [])));
  $now = gmdate('c');
  $post = [
    'slug' => $slug,
    'title' => $title,
    'excerpt' => ($postData['excerpt'] ?? null) === '' ? null : ($postData['excerpt'] ?? null),
    'body' => $body,
    'status' => 'published',
    'publishedAt' => $now,
    'expiresAt' => null,
    'updatedAt' => $now,
    'authorName' => ($postData['authorName'] ?? null) === '' ? null : ($postData['authorName'] ?? null),
    'seoTitle' => null,
    'seoDescription' => null,
    'legacyWpId' => null,
    'cover' => null,
    'categories' => $categories,
    'tags' => [],
  ];
  if (!write_json_atomic($path, $post)) {
    respond(500, ['error' => 'Не удалось сохранить']);
  }
  respond(200, ['ok' => true, 'slug' => $slug]);
}

if ($action === 'update') {
  $slug = trim((string) ($postData['slug'] ?? ''));
  if (!slug_ok($slug)) {
    respond(422, ['error' => 'Некорректный адрес записи']);
  }
  $path = $root . '/posts/' . $slug . '.json';
  $existing = read_json_file($path);
  if (!is_array($existing)) {
    respond(404, ['error' => 'Запись не найдена']);
  }
  if (isset($postData['title'])) {
    $existing['title'] = trim((string) $postData['title']);
  }
  if (isset($postData['body'])) {
    $existing['body'] = sanitize_body((string) $postData['body']);
  }
  if (array_key_exists('excerpt', $postData)) {
    $existing['excerpt'] = $postData['excerpt'] === '' ? null : $postData['excerpt'];
  }
  if (array_key_exists('authorName', $postData)) {
    $existing['authorName'] = $postData['authorName'] === '' ? null : $postData['authorName'];
  }
  if (isset($postData['categories']) && is_array($postData['categories'])) {
    $existing['categories'] = array_values(array_map('strval', $postData['categories']));
  }
  $existing['updatedAt'] = gmdate('c');
  if (!write_json_atomic($path, $existing)) {
    respond(500, ['error' => 'Не удалось сохранить']);
  }
  respond(200, ['ok' => true, 'slug' => $slug]);
}

respond(400, ['error' => 'Некорректный запрос']);
