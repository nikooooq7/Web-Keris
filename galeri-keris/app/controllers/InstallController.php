<?php
/**
 * InstallController — installer otomatis (alamat: /install).
 * Membuat tabel database, akun admin, dan file config/config.php.
 * Hanya bisa dibuka selama website belum diinstal.
 */
class InstallController extends Controller
{
    public function form()
    {
        $this->view('install/form', ['checks' => $this->requirements()], 'layouts/install');
    }

    /** [label, lolos?, wajib?] */
    private function requirements(): array
    {
        return [
            ['PHP 7.4 atau lebih baru (server Anda: ' . PHP_VERSION . ')', version_compare(PHP_VERSION, '7.4.0', '>='), true],
            ['Ekstensi PDO MySQL (pdo_mysql)', extension_loaded('pdo_mysql'), true],
            ['Folder public/uploads dapat ditulis (untuk foto & video)', is_writable(UPLOAD_PATH), true],
            ['Folder config dapat ditulis (untuk menyimpan pengaturan)', is_writable(CONFIG_PATH), false],
            ['Folder storage dapat ditulis (untuk catatan error)', is_writable(STORAGE_PATH), false],
            ['Ekstensi GD (pengecil ukuran foto otomatis)', extension_loaded('gd'), false],
            ['Ekstensi fileinfo (pemeriksa jenis file upload)', extension_loaded('fileinfo'), false],
        ];
    }

    public function run()
    {
        $host = post_str('db_host', 120) ?: 'localhost';
        $dbName = post_str('db_name', 100);
        $dbUser = post_str('db_user', 100);
        $dbPass = (string) ($_POST['db_pass'] ?? '');
        $shop = post_str('shop_name', 80);
        $adminUser = post_str('admin_username', 40);
        $adminPass = (string) ($_POST['admin_password'] ?? '');
        $adminPass2 = (string) ($_POST['admin_password_confirm'] ?? '');
        $sample = isset($_POST['sample']);

        $errors = [];
        if ($dbName === '') {
            $errors[] = 'Nama database wajib diisi.';
        }
        if ($dbUser === '') {
            $errors[] = 'User database wajib diisi.';
        }
        if ($shop === '') {
            $errors[] = 'Nama toko wajib diisi.';
        }
        if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $adminUser)) {
            $errors[] = 'Username admin 3–40 karakter, hanya huruf, angka, titik, minus, dan garis bawah.';
        }
        if (strlen($adminPass) < 8) {
            $errors[] = 'Password admin minimal 8 karakter.';
        }
        if ($adminPass !== $adminPass2) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }
        if (!extension_loaded('pdo_mysql')) {
            $errors[] = 'Ekstensi PHP "pdo_mysql" belum aktif di server Anda. Aktifkan lewat pengaturan PHP di hosting (lihat PANDUAN.md).';
        }
        if ($errors) {
            $this->backWithErrors($errors, 'install');
        }

        // Dukung penulisan "host:port"
        $port = 3306;
        if (strpos($host, ':') !== false) {
            list($host, $p) = explode(':', $host, 2);
            $port = (int) $p ?: 3306;
        }
        $dbConfig = [
            'driver' => 'mysql',
            'host'   => $host,
            'port'   => $port,
            'name'   => $dbName,
            'user'   => $dbUser,
            'pass'   => $dbPass,
        ];

        // 1. Uji koneksi
        try {
            $pdo = Database::connect($dbConfig);
        } catch (Throwable $e) {
            $this->backWithErrors([
                'Tidak dapat terhubung ke database. Periksa lagi nama database, user, password, dan host. '
                . '(Pesan dari server: ' . $e->getMessage() . ')',
            ], 'install');
            return;
        }

        // Selama proses instalasi, model memakai koneksi ini
        $config = [
            'db'     => $dbConfig,
            'app'    => ['debug' => false, 'timezone' => 'Asia/Jakarta'],
            'upload' => ['max_image_mb' => 10, 'max_video_mb' => 100],
        ];
        $GLOBALS['APP_CONFIG'] = $config;

        try {
            // 2. Buat tabel
            Database::runSqlFile($pdo, ROOT_PATH . '/database/schema.sql');

            // 3. Akun admin (jika username sudah ada, password diperbarui)
            $existing = User::findByUsername($adminUser);
            if ($existing) {
                User::setPassword((int) $existing['id'], $adminPass);
            } else {
                User::create($adminUser, $adminPass, $adminUser);
            }

            // 4. Nama toko
            Setting::set('shop_name', $shop);

            // 5. Contoh data (opsional)
            if ($sample && (int) Database::value('SELECT COUNT(*) FROM categories') === 0
                && (int) Database::value('SELECT COUNT(*) FROM products') === 0) {
                $this->seedSample();
            }
        } catch (Throwable $e) {
            log_error($e);
            $GLOBALS['APP_CONFIG'] = [];
            $this->backWithErrors(['Gagal menyiapkan database: ' . $e->getMessage()], 'install');
            return;
        }

        // 6. Simpan config/config.php
        $content = "<?php\n// File ini dibuat otomatis oleh installer pada " . date('Y-m-d H:i:s') . ".\n"
            . "// Berisi data koneksi database — JANGAN dibagikan ke siapa pun.\n"
            . 'return ' . var_export($config, true) . ";\n";

        if (@file_put_contents(CONFIG_PATH . '/config.php', $content, LOCK_EX) === false) {
            // Folder config tidak bisa ditulis: tampilkan isinya agar bisa disalin manual
            $GLOBALS['APP_CONFIG'] = [];
            $this->view('install/manual', ['content' => $content], 'layouts/install');
            return;
        }
        @chmod(CONFIG_PATH . '/config.php', 0644);

        Flash::add('success', 'Instalasi berhasil! Silakan masuk dengan akun admin yang baru Anda buat.');
        redirect('admin/login');
    }

    /** Contoh kategori & produk agar tampilan website langsung terlihat */
    private function seedSample(): void
    {
        $cats = [
            'Keris'                    => 'Keris pusaka dengan berbagai dhapur dan pamor.',
            'Tombak & Senjata Pusaka'  => 'Tombak, wedung, dan senjata tradisional bersejarah.',
            'Perhiasan & Cincin Antik' => 'Cincin, liontin, dan perhiasan antik.',
            'Keramik & Perabot Antik'  => 'Guci, porselen, dan perabot antik pilihan.',
        ];
        $ids = [];
        $sort = 1;
        foreach ($cats as $name => $desc) {
            $ids[$name] = Category::create($name, $desc, $sort++);
        }

        $note = "\n\nCATATAN: ini adalah produk CONTOH agar Anda bisa melihat tampilan website. "
            . 'Silakan ubah atau hapus lewat Panel Admin → Produk, lalu tambahkan produk asli Anda beserta foto dan videonya.';
        $items = [
            ['Keris Luk 7 Pamor Beras Wutah (Contoh)', 'Keris', 12500000, 'tersedia', 1, 'Jawa Tengah', 'Abad ke-19', 'Besi, baja, dan nikel (pamor)', 'Panjang bilah ± 36 cm', 'Terawat, warangan baru',
                "Keris berluk tujuh dengan pamor beras wutah yang tersebar rapat di sepanjang bilah. Dilengkapi warangka dan hulu kayu.\n\nPamor beras wutah dipercaya melambangkan kemakmuran dan rezeki yang melimpah."],
            ['Keris Luk 9 Pamor Udan Mas (Contoh)', 'Keris', 18000000, 'tersedia', 1, 'Jawa Timur', 'Abad ke-18', 'Besi, baja, dan nikel (pamor)', 'Panjang bilah ± 38 cm', 'Sangat terawat',
                "Keris berluk sembilan dengan pamor udan mas — bintik-bintik pamor yang menyerupai hujan emas.\n\nBilah dalam kondisi baik dengan ricikan yang jelas."],
            ['Tombak Pusaka Pamor Ngulit Semangka (Contoh)', 'Tombak & Senjata Pusaka', 7500000, 'tersedia', 1, 'Jawa', 'Abad ke-19', 'Besi dan nikel (pamor)', 'Panjang mata tombak ± 28 cm', 'Baik',
                "Mata tombak dengan pamor ngulit semangka yang menyerupai kulit buah semangka, lengkap dengan landeyan."],
            ['Cincin Perak Antik Batu Akik (Contoh)', 'Perhiasan & Cincin Antik', 1750000, 'tersedia', 0, 'Nusantara', 'Awal abad ke-20', 'Perak dan batu akik', 'Ukuran 18', 'Baik',
                "Cincin perak antik dengan ukiran tangan dan batu akik alami."],
            ['Guci Keramik Antik Motif Naga (Contoh)', 'Keramik & Perabot Antik', 3200000, 'dipesan', 0, 'Asia', 'Awal abad ke-20', 'Keramik', 'Tinggi ± 45 cm', 'Utuh, tanpa retak',
                "Guci keramik dengan motif naga berlapis glasir. Cocok sebagai pajangan ruang tamu."],
            ['Wedung Antik Gagang Tanduk (Contoh)', 'Tombak & Senjata Pusaka', 2400000, 'terjual', 0, 'Jawa', 'Awal abad ke-20', 'Besi dan tanduk', 'Panjang ± 30 cm', 'Baik',
                "Wedung tradisional dengan gagang tanduk berukir. Produk ini ditandai TERJUAL sebagai contoh tampilan label status."],
        ];
        foreach ($items as $it) {
            Product::create([
                'category_id'    => $ids[$it[1]] ?? null,
                'name'           => $it[0],
                'price'          => $it[2],
                'status'         => $it[3],
                'is_featured'    => $it[4],
                'is_active'      => 1,
                'origin'         => $it[5],
                'era'            => $it[6],
                'material'       => $it[7],
                'dimension'      => $it[8],
                'condition_note' => $it[9],
                'description'    => $it[10] . $note,
                'video_url'      => '',
                'views'          => 0,
            ]);
        }
    }
}
