<?php
/**
 * FRONT CONTROLLER — semua kunjungan masuk lewat file ini.
 * Biasanya Anda tidak perlu mengubahnya.
 */

// Saat memakai server bawaan PHP (php -S), file statis (gambar/css/js) dilayani langsung
if (PHP_SAPI === 'cli-server') {
    $p = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($p !== '/' && is_file(__DIR__ . $p)) {
        return false;
    }
}

require __DIR__ . '/../app/bootstrap.php';

$router = new Router();
require APP_PATH . '/routes.php';

$path = request_path();

// Belum diinstal → arahkan ke installer. Sudah diinstal → installer ditutup.
if (!app_installed()) {
    if ($path !== '/install') {
        redirect('install');
    }
} elseif ($path === '/install') {
    redirect('');
}

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
