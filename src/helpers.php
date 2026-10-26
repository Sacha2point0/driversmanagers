<?php
declare(strict_types=1);

use App\Core\Config;

/** Échappement HTML. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL absolue dans l'application. */
function url(string $path = ''): string
{
    $base = rtrim((string) Config::get('app.base_url', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $file = ROOT . '/public/assets/' . ltrim($path, '/');
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Session expirée : rechargez la page et recommencez.');
    }
}

/** Charge un fichier de contenu (content/*.php). */
function content(string $name): array
{
    static $cache = [];
    return $cache[$name] ??= require ROOT . '/content/' . $name . '.php';
}

function json_response(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Espaces insécables avant la ponctuation double (typographie française). */
function fr(string $s): string
{
    return strtr($s, [' ?' => "\u{202F}?", ' !' => "\u{202F}!", ' :' => "\u{00A0}:", ' ;' => "\u{202F};", '« ' => "«\u{00A0}", ' »' => "\u{00A0}»"]);
}
