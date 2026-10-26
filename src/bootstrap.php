<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

// Autoload PSR-4 minimal (pas de Composer) : App\Foo\Bar => src/Foo/Bar.php
spl_autoload_register(function (string $class): void {
    if (strncmp($class, 'App\\', 4) !== 0) {
        return;
    }
    $file = ROOT . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$configFile = ROOT . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Configuration manquante : copiez config/config.example.php en config/config.php.');
}
App\Core\Config::load(require $configFile);

date_default_timezone_set(App\Core\Config::get('app.timezone', 'Europe/Paris'));

if (App\Core\Config::get('app.debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}
ini_set('log_errors', '1');
ini_set('error_log', ROOT . '/storage/logs/php-error.log');

if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli') {
    session_name('permis_sess');
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 60,   // 60 jours : on reste connecté du questionnaire au jour J
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.gc_maxlifetime', (string) (60 * 60 * 24 * 60));
    session_start();
}

require ROOT . '/src/helpers.php';
