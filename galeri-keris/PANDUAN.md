# PANDUAN LENGKAP — Website Galeri Keris & Barang Antik

Panduan ini ditulis untuk pemula. Ikuti urutannya dan jangan melompat.

**Daftar isi**
1. [Isi paket & arti setiap folder](#1-isi-paket--arti-setiap-folder)
2. [Mencoba di komputer sendiri (XAMPP)](#2-mencoba-di-komputer-sendiri-xampp)
3. [Memakai Panel Admin (tanpa coding)](#3-memakai-panel-admin-tanpa-coding)
4. [Hosting: tutorial cPanel langkah demi langkah](#4-hosting-tutorial-cpanel-langkah-demi-langkah)
5. [Setelah website online](#5-setelah-website-online)
6. [Masalah umum & solusinya](#6-masalah-umum--solusinya)
7. [Mengedit kode (jika suatu saat perlu)](#7-mengedit-kode-jika-suatu-saat-perlu)

---

## 1. Isi paket & arti setiap folder

Website ini **tidak dibuat dalam satu file**. Backend (otak/logika), tampilan, dan framework CSS dipisah rapi (pola MVC sederhana) agar mudah diedit:

```
galeri-keris/
├── public/                  ← SATU-SATUNYA folder yang boleh dibuka pengunjung
│   ├── index.php            (pintu masuk website)
│   ├── assets/
│   │   ├── vendor/bootstrap/   ← FRAMEWORK CSS Bootstrap (terpisah, jangan diubah)
│   │   ├── css/style.css       ← desain halaman publik (warna emas, dll.)
│   │   ├── css/admin.css       ← desain Panel Admin
│   │   ├── js/                 ← JavaScript kecil
│   │   └── img/                ← logo, keris emas berpamor (SVG), pola batik
│   └── uploads/             ← foto & video yang Anda unggah (terisi otomatis)
│
├── app/                     ← BACKEND (tidak bisa dibuka lewat browser)
│   ├── routes.php           ← daftar alamat halaman
│   ├── settings_fields.php  ← daftar isian yang bisa diubah di Panel Admin
│   ├── controllers/         ← logika tiap halaman (C)
│   ├── models/              ← akses database: Product, Category, Setting... (M)
│   ├── views/               ← TAMPILAN HTML tiap halaman (V)
│   └── core/                ← mesin dasar (database, keamanan, upload)
│
├── config/config.php        ← data koneksi database (dibuat otomatis oleh installer)
├── database/schema.sql      ← rancangan tabel database MySQL
└── storage/logs/error.log   ← catatan error (jika ada masalah)
```

**Aturan emas:** teks, foto, kontak, produk → ubah lewat **Panel Admin**. Kode hanya disentuh jika Anda ingin mengubah desain atau menambah fitur.

---

## 2. Mencoba di komputer sendiri (XAMPP)

Sangat disarankan mencoba dulu di komputer sebelum hosting.

1. Unduh & pasang **XAMPP** (versi PHP 8.x) dari apachefriends.org.
2. Buka *XAMPP Control Panel* → klik **Start** pada **Apache** dan **MySQL**.
3. Salin folder `galeri-keris` ke `C:\xampp\htdocs\`.
4. Buka **http://localhost/phpmyadmin** → tab **Databases** → isi nama `galeri_keris` → **Create**.
5. Buka **http://localhost/galeri-keris/** → Anda otomatis diarahkan ke halaman **Instalasi**.
6. Isi formulir:
   - Host: `localhost`
   - Nama database: `galeri_keris`
   - User database: `root`
   - Password database: *(kosongkan)*
   - Nama toko, username & password admin (**catat baik-baik**).
7. Klik **Pasang Website Sekarang**. Selesai! Masuk admin di **http://localhost/galeri-keris/admin**.

> Jika alamat cantik (mis. `/koleksi`) menghasilkan "Not Found", buka `C:\xampp\apache\conf\httpd.conf`, cari `AllowOverride None` pada bagian `htdocs`, ubah menjadi `AllowOverride All`, lalu Restart Apache.

---

## 3. Memakai Panel Admin (tanpa coding)

Alamat: `https://domainanda.com/admin`

| Menu | Gunanya |
|---|---|
| **Dashboard** | Ringkasan + daftar "Langkah Awal" |
| **Produk** | Tambah/ubah/hapus barang: **foto (banyak), video, harga, deskripsi**, asal, era, bahan, ukuran, kondisi, status (Tersedia/Dipesan/Terjual), tampil/sembunyi, koleksi pilihan |
| **Kategori** | Keris, Tombak, Keramik, dll. |
| **Profil Toko** | **Nama toko, foto profil (orang/toko), foto latar belakang galeri**, tentang kami, sejarah, visi, misi |
| **Teks Beranda** | Judul besar, tombol, keunggulan, ajakan di halaman depan |
| **Kontak & Sosial** | Alamat, telepon, WhatsApp, email, jam buka, peta Google Maps, Instagram/Facebook/TikTok/YouTube |
| **Tampilan & SEO** | Logo, teks footer, deskripsi untuk Google, jumlah produk per halaman |
| **Pesan Masuk** | Pesan dari formulir Kontak pengunjung |
| **Akun Admin** | Ganti username & password |

**Menambah produk (contoh):** Produk → *Tambah Produk* → isi nama, kategori, harga (ketik angka saja, titik otomatis) → tulis deskripsi → pilih beberapa foto sekaligus → (opsional) pilih video atau tempel tautan YouTube → *Simpan Produk*.

**Tips video:** video dari HP biasanya sangat besar. Cara termudah: unggah ke **YouTube** (pilih "Tidak publik"), lalu tempel tautannya di kolom *tautan video YouTube*. Video MP4 kecil (di bawah batas server) juga bisa diunggah langsung.

**Foto:** foto besar dari HP otomatis dikecilkan agar website cepat. Foto pertama = foto sampul.

---

## 4. Hosting: tutorial cPanel langkah demi langkah

Hosting di Indonesia (Niagahoster, Hostinger, Rumahweb, IDCloudHost, dll.) umumnya memakai **cPanel**. Langkah di bawah berlaku untuk hampir semuanya. Anda butuh: **paket hosting PHP + MySQL** dan **nama domain** (biasanya satu paket).

### Langkah 1 — Siapkan file
1. Pastikan folder `galeri-keris` **belum** berisi `config/config.php` (jika Anda sudah mencoba di XAMPP, **hapus** file itu dari salinan yang akan diunggah, atau installer tidak akan muncul).
2. Hapus isi `public/uploads/` selain file `.htaccess` (agar foto percobaan tidak ikut), dan kosongkan `storage/logs/`.
3. Kompres isi folder `galeri-keris` menjadi **galeri-keris.zip** (isi foldernya, bukan lapisan folder tambahan).

### Langkah 2 — Atur versi PHP
cPanel → **Select PHP Version** (atau *MultiPHP Manager*) → pilih **PHP 8.1 / 8.2 / 8.3** (minimal 7.4).
Pastikan ekstensi ini tercentang: `pdo_mysql`, `mysqli`, `gd`, `fileinfo`, `mbstring`, `exif`.

### Langkah 3 — Buat database MySQL
1. cPanel → **MySQL® Databases** (atau *MySQL Database Wizard*).
2. **Create New Database** → nama mis. `galerikeris` → Anda mendapat nama lengkap seperti `u123456_galerikeris`. **Catat.**
3. **MySQL Users → Add New User** → buat username & password kuat (**catat**).
4. **Add User To Database** → pilih user & database tadi → centang **ALL PRIVILEGES** → Make Changes.

### Langkah 4 — Unggah file
1. cPanel → **File Manager** → buka folder **public_html** (atau folder domain Anda).
2. Klik **Upload** → pilih `galeri-keris.zip` → tunggu selesai.
3. Klik kanan zip → **Extract**. Pastikan hasilnya: `public_html/app`, `public_html/public`, `public_html/config`, `public_html/.htaccess`, dll.
   (Aktifkan *Show Hidden Files* di Settings File Manager agar `.htaccess` terlihat.)
4. Hapus file zip.

> **Cara paling aman (disarankan jika tersedia):** di cPanel → **Domains** → ubah **Document Root** domain Anda menjadi `public_html/public`. Dengan begitu folder `app`, `config`, dan `database` sama sekali tidak terjangkau dari internet.
> Jika menu itu tidak ada, tidak masalah: file `.htaccess` di folder paling luar otomatis mengarahkan pengunjung ke folder `public` dan memblokir folder rahasia.

### Langkah 5 — Jalankan installer
1. Buka **https://domainanda.com** → otomatis menuju halaman **Instalasi**.
2. Periksa bagian *Pemeriksaan Server* — semua yang bertanda wajib harus ✓.
3. Isi data database dari Langkah 3:
   - Host: `localhost`
   - Nama database & user: **nama lengkap** (dengan awalan `u123456_`)
   - Password: milik user database tadi
4. Isi nama toko serta username & password admin.
5. Centang/biarkan *contoh data* jika ingin melihat tampilan dulu (bisa dihapus nanti), lalu **Pasang Website Sekarang**.
6. Masuk ke **https://domainanda.com/admin**.

Setelah terpasang, halaman `/install` otomatis tertutup.

> Jika muncul halaman "Satu langkah terakhir (manual)": salin kode yang ditampilkan ke file baru `config/config.php` via File Manager, lalu simpan.

### Langkah 6 — Aktifkan HTTPS (gembok hijau)
cPanel → **SSL/TLS Status** → *Run AutoSSL*. Atau di dashboard hosting, aktifkan **Free SSL (Let's Encrypt)**. Setelah aktif, buka website dengan `https://`.

### Langkah 7 — Isi website
Masuk admin, ikuti daftar **Langkah Awal** di Dashboard: unggah foto profil & foto latar galeri, isi WhatsApp & alamat, hapus produk contoh, tambahkan produk asli.

### Jika hosting Anda memakai Nginx / VPS
File `.htaccess` tidak berlaku di Nginx. Arahkan `root` ke folder `public` dan tambahkan:
```nginx
location / { try_files $uri $uri/ /index.php?$query_string; }
location ~ \.php$ { include fastcgi_params; fastcgi_pass unix:/run/php/php8.2-fpm.sock; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; }
location ~ /\.(?!well-known) { deny all; }
location ^~ /uploads/ { location ~ \.php$ { deny all; } }
client_max_body_size 72M;
```
(Sesuaikan versi/socket PHP-FPM Anda. Untuk VPS, minta bantuan penyedia atau teknisi bila belum terbiasa.)

---

## 5. Setelah website online

**Backup (wajib rutin, mis. sebulan sekali):**
1. cPanel → **phpMyAdmin** → pilih database → tab **Export** → *Go* (menyimpan semua data produk & pengaturan).
2. File Manager → kompres folder `public/uploads` → unduh (menyimpan semua foto & video).
3. Simpan keduanya di Google Drive/flashdisk. Banyak hosting juga menyediakan menu **Backup**.

**Keamanan:**
- Gunakan password admin yang panjang & unik. Salah password 5 kali = dikunci 5 menit.
- Jangan membagikan file `config/config.php`.
- Jika website tampak bermasalah, lihat `storage/logs/error.log` untuk pesan penyebabnya.

**Memindahkan website ke hosting lain:** ulangi Langkah 3–5, tetapi **Import** hasil export database lewat phpMyAdmin dan salin folder `uploads`, lalu buat `config/config.php` dari `config.sample.php` sesuai database baru.

---

## 6. Masalah umum & solusinya

| Gejala | Penyebab & solusi |
|---|---|
| Halaman putih / "500 Internal Server Error" | Buka `storage/logs/error.log`. Biasanya versi PHP terlalu lama (pilih ≥ 7.4) atau ekstensi belum aktif (Langkah 2). |
| Installer: "Tidak dapat terhubung ke database" | Nama database/user harus **lengkap dengan awalan** `u123456_`; user harus sudah ditambahkan ke database (Langkah 3 no. 4); host biasanya `localhost`. |
| Installer: ✗ "Folder public/uploads dapat ditulis" | File Manager → klik kanan folder `uploads` → *Change Permissions* → `755` (atau `775`). |
| Halaman selain Beranda "404 Not Found" | `.htaccess` tidak terbaca: pastikan file `.htaccess` ikut terunggah (Show Hidden Files) dan hosting Anda memakai Apache/LiteSpeed. |
| Tampilan berantakan/tanpa warna | File `public/assets` belum terunggah lengkap, atau Document Root salah. Buka `domain/assets/css/style.css` — harus menampilkan kode CSS. |
| **Upload video/foto gagal / "terlalu besar"** | Batas upload server kecil. (a) File `public/.user.ini` sudah menaikkannya ke 64 MB — tunggu ±5 menit lalu coba lagi. (b) Atau cPanel → *Select PHP Version → Options* → ubah `upload_max_filesize` & `post_max_size`. (c) Cara termudah: pakai **tautan YouTube** untuk video. |
| Peta Google Maps tidak tampil | Pakai kode **Sematkan peta** (Embed a map), bukan tautan biasa. Harus berawalan `https://www.google.com/maps/embed?...` |
| Lupa password admin | Hapus file `config/config.php` (data produk & foto **tidak** terhapus), buka kembali website → installer muncul. Isi data database yang sama dan **username admin yang sama** dengan password baru → password diperbarui, semua data tetap aman. Catatan: nama toko akan ikut terisi ulang dengan yang Anda ketik. |
| Ingin melihat pesan error detail | Di `config/config.php` ubah `'debug' => false` menjadi `true`. **Kembalikan ke false** setelah selesai. |

---

## 7. Mengedit kode (jika suatu saat perlu)

| Ingin mengubah... | Buka file |
|---|---|
| Warna emas / font / gaya halaman publik | `public/assets/css/style.css` (variabel warna di bagian atas `:root`) |
| Menu atas & footer | `app/views/layouts/public.php` |
| Isi Beranda | `app/views/home.php` |
| Halaman Koleksi / Detail produk | `app/views/catalog/index.php`, `show.php` |
| Kartu produk | `app/views/partials/product_card.php` |
| Halaman Profil Kami / Kontak | `app/views/pages/about.php`, `contact.php` |
| Isian baru di Panel Admin (mis. "Nomor Rekening") | `app/settings_fields.php` → tambah satu baris; tampilkan di halaman dengan `<?= e(setting('nama_kunci')) ?>` |
| Kolom baru pada produk | `database/schema.sql` (+ jalankan `ALTER TABLE` di phpMyAdmin), `AdminProductController::collect()`, `views/admin/products/form.php` |
| Menambah halaman baru | `app/routes.php` + fungsi di controller + file di `app/views/` |
| Ornamen keris emas | `public/assets/img/*.svg` (bisa diganti dengan file Anda sendiri dengan nama sama) |
| Mengganti Bootstrap ke versi lain | Ganti file di `public/assets/vendor/bootstrap/` |

Setiap file PHP diberi komentar penjelasan di bagian atasnya.

**Catatan Bootstrap:** folder `vendor/bootstrap` berisi Bootstrap 5.3.2 yang disertakan lokal (website tidak bergantung pada CDN/internet pihak ketiga). Berkas CSS-nya adalah build Bootstrap dengan lapisan tema bawaan, namun seluruh warna dan gayanya sudah ditimpa oleh `style.css` dan `admin.css`. Jika Anda ingin memakai Bootstrap resmi polos, unduh dari getbootstrap.com lalu ganti `bootstrap.min.css` dan `bootstrap.bundle.min.js` di folder tersebut. Setelah mengganti, periksa tampilan karena beberapa penyesuaian bisa bergeser.

Selamat berkarya, semoga galerinya ramai pengunjung! 🙏
