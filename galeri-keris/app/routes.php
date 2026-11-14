<?php
/**
 * DAFTAR ALAMAT (ROUTES)
 * ---------------------------------------------------------------
 * Format:  $router->get('/alamat', 'NamaController@namaFungsi');
 *          $router->post(...) dipakai untuk pengiriman form.
 * {id} = angka, {slug} = teks pada alamat.
 *
 * Untuk menambah halaman baru: tambahkan satu baris di sini, buat fungsi di
 * controller, lalu buat file tampilannya di app/views/.
 */

/* ---- Halaman publik ---- */
$router->get('/',                  'HomeController@index');
$router->get('/koleksi',           'CatalogController@index');
$router->get('/koleksi/{slug}',    'CatalogController@show');
$router->get('/profil',            'PageController@about');
$router->get('/kontak',            'PageController@contact');
$router->post('/kontak',           'PageController@sendMessage');

/* ---- Installer (hanya aktif sebelum website diinstal) ---- */
$router->get('/install',           'InstallController@form');
$router->post('/install',          'InstallController@run');

/* ---- Panel Admin ---- */
$router->get('/admin/login',       'AdminAuthController@loginForm');
$router->post('/admin/login',      'AdminAuthController@login');
$router->post('/admin/logout',     'AdminAuthController@logout');

$router->get('/admin',             'AdminDashboardController@index');

// Produk
$router->get('/admin/produk',                 'AdminProductController@index');
$router->get('/admin/produk/tambah',          'AdminProductController@create');
$router->post('/admin/produk/tambah',         'AdminProductController@store');
$router->get('/admin/produk/{id}/edit',       'AdminProductController@edit');
$router->post('/admin/produk/{id}/edit',      'AdminProductController@update');
$router->post('/admin/produk/{id}/hapus',     'AdminProductController@destroy');

// Kategori
$router->get('/admin/kategori',               'AdminCategoryController@index');
$router->post('/admin/kategori/tambah',       'AdminCategoryController@store');
$router->get('/admin/kategori/{id}/edit',     'AdminCategoryController@edit');
$router->post('/admin/kategori/{id}/edit',    'AdminCategoryController@update');
$router->post('/admin/kategori/{id}/hapus',   'AdminCategoryController@destroy');

// Pengaturan (profil toko, beranda, kontak, tampilan)
$router->get('/admin/profil',                 'AdminSettingsController@profile');
$router->post('/admin/profil',                'AdminSettingsController@saveProfile');
$router->get('/admin/beranda',                'AdminSettingsController@home');
$router->post('/admin/beranda',               'AdminSettingsController@saveHome');
$router->get('/admin/kontak',                 'AdminSettingsController@contact');
$router->post('/admin/kontak',                'AdminSettingsController@saveContact');
$router->get('/admin/tampilan',               'AdminSettingsController@site');
$router->post('/admin/tampilan',              'AdminSettingsController@saveSite');

// Pesan masuk
$router->get('/admin/pesan',                  'AdminMessageController@index');
$router->get('/admin/pesan/{id}',             'AdminMessageController@show');
$router->post('/admin/pesan/{id}/hapus',      'AdminMessageController@destroy');

// Akun admin
$router->get('/admin/akun',                   'AdminAccountController@form');
$router->post('/admin/akun',                  'AdminAccountController@update');
