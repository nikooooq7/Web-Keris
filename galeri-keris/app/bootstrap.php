<?php
/**
 * BOOTSTRAP
 * ---------------------------------------------------------------
 * File pertama yang dijalankan setiap ada pengunjung.
 * Tugasnya: menentukan lokasi folder, memuat class otomatis,
 * memuat konfigurasi, dan menyiapkan penanganan error + sesi.
 *
 * Biasanya Anda TIDAK perlu mengubah file ini.
 */

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('Website ini membutuhkan PHP 7.4 atau lebih baru. Versi PHP di server Anda: ' . PHP_VERSION);
}

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// Memuat class (Product, Router, dll.) secara otomatis dari folder app/
spl_autoload_register(function ($class) {
    foreach (['core', 'models', 'controllers', 'controllers/admin'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

require APP_PATH . '/core/helpers.php';

// Konfigurasi (dibuat otomatis oleh installer: config/config.php)
$configFile = CONFIG_PATH . '/config.php';
$GLOBALS['APP_CONFIG'] = is_file($configFile) ? (require $configFile) : [];

date_default_timezone_set((string) app_config('app.timezone', 'Asia/Jakarta'));
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// Penanganan error: error tidak ditampilkan ke pengunjung, tetapi dicatat di storage/logs/error.log
$debug = (bool) app_config('app.debug', false);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');

set_exception_handler(function ($e) use ($debug) {
    log_error($e);
    if (!headers_sent()) {
        http_response_code(500);
    }
    try {
        render_view('errors/500', ['debugMessage' => $debug ? $e->getMessage() : ''], null);
    } catch (Throwable $inner) {
        echo '<h1>Terjadi kesalahan pada server</h1><p>Silakan coba lagi beberapa saat lagi.</p>';
    }
});

// Memulai sesi login + pesan notifikasi
Auth::startSession();
