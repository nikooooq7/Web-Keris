<?php
/**
 * CONTOH KONFIGURASI
 * ---------------------------------------------------------------
 * Biasanya Anda TIDAK perlu menyentuh file ini: installer (alamat /install)
 * akan membuat file config/config.php secara otomatis.
 *
 * Gunakan file ini hanya jika installer tidak bisa menulis file:
 * salin isinya menjadi config/config.php lalu isi data database Anda.
 */
return [
    'db' =>  [
        'driver'  => 'mysql',
        'host'    => 'localhost',     // biasanya "localhost"
        'port'    => 3306,
        'name'    => 'galeri_keris2', // nama database yang Anda buat di hosting
        'user'    => 'root', // user database
        'pass'    => '',
    ],
    'app' => [
        'debug'    => false,          // ubah ke true HANYA saat mencari penyebab error
        'timezone' => 'Asia/Jakarta',
    ],
    'upload' => [
        'max_image_mb' => 10,         // batas ukuran satu foto
        'max_video_mb' => 100,        // batas ukuran satu video
    ],
];
