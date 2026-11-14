<?php
/**
 * DAFTAR PENGATURAN WEBSITE
 * ---------------------------------------------------------------
 * File ini menentukan isian apa saja yang bisa diubah dari Panel Admin
 * (menu Profil Toko, Beranda, Kontak & Sosial, Tampilan & SEO).
 *
 * Setiap isian punya:
 *   'label'    => nama isian yang tampil di form
 *   'type'     => text | textarea | email | url | tel | number | image
 *   'default'  => nilai bawaan (dipakai sebelum Anda mengubahnya di Panel Admin)
 *   'help'     => teks bantuan kecil di bawah isian (opsional)
 *   'required' => true jika wajib diisi (opsional)
 *   'max'      => batas jumlah karakter (opsional)
 *
 * Ingin menambah isian baru? Cukup tambahkan satu baris di sini, lalu
 * tampilkan di halaman dengan:  <?= e(setting('nama_kunci')) ?>
 */
return [

    /* ==================================================================
     * PROFIL TOKO  (menu: Profil Toko)
     * ================================================================== */
    'profile' => [
        'title' => 'Profil Toko',
        'intro' => 'Data inti toko Anda. Tampil di halaman Profil Kami, Beranda, dan bagian bawah (footer) website.',
        'sections' => [
            'Identitas Toko' => [
                'shop_name' => [
                    'label' => 'Nama Toko / Galeri', 'type' => 'text', 'required' => true, 'max' => 80,
                    'default' => 'Galeri Keris Nusantara',
                    'help' => 'Tampil di menu atas, judul tab browser, dan footer.',
                ],
                'tagline' => [
                    'label' => 'Slogan', 'type' => 'text', 'max' => 140,
                    'default' => 'Pusaka Adiluhung, Warisan Kejayaan Nusantara',
                ],
                'owner_name' => [
                    'label' => 'Nama Pemilik / Kurator', 'type' => 'text', 'max' => 80,
                    'default' => '',
                    'help' => 'Opsional. Jika diisi, tampil di halaman Profil Kami.',
                ],
                'profile_photo' => [
                    'label' => 'Foto Profil (foto pemilik atau foto toko)', 'type' => 'image', 'max_dim' => 900,
                    'default' => 'assets/img/logo.svg',
                    'help' => 'Disarankan foto persegi (rasio 1:1), format JPG/PNG/WEBP.',
                ],
                'gallery_background' => [
                    'label' => 'Foto Latar Belakang Galeri', 'type' => 'image', 'max_dim' => 2000,
                    'default' => 'assets/img/bg-hero.svg',
                    'help' => 'Foto lebar (landscape) dari galeri/toko Anda. Dipakai sebagai latar Beranda dan halaman Profil Kami.',
                ],
            ],
            'Tentang Toko' => [
                'about_text' => [
                    'label' => 'Tentang Kami', 'type' => 'textarea', 'rows' => 6, 'max' => 3000,
                    'default' => "Galeri Keris Nusantara adalah rumah bagi pusaka-pusaka pilihan: keris, tombak, wedung, serta benda-benda antik lainnya yang dikurasi dengan teliti.\n\nSetiap benda kami sertai penjelasan yang jujur mengenai asal-usul, pamor, dhapur, bahan, dan kondisinya, sehingga para pecinta maupun kolektor dapat memiliki dengan tenang dan bangga.",
                    'help' => 'Tekan Enter dua kali untuk membuat paragraf baru.',
                ],
                'history' => [
                    'label' => 'Sejarah Singkat', 'type' => 'textarea', 'rows' => 5, 'max' => 3000,
                    'default' => "Berawal dari kecintaan terhadap warisan leluhur, galeri ini hadir untuk menjaga agar pusaka dan benda antik Nusantara tetap lestari, dihargai, dan berpindah ke tangan yang tepat.\n\n(Silakan ubah teks ini di Panel Admin → Profil Toko dengan sejarah toko Anda.)",
                ],
                'vision' => [
                    'label' => 'Visi', 'type' => 'textarea', 'rows' => 3, 'max' => 1000,
                    'default' => 'Menjadi galeri pusaka dan barang antik yang terpercaya, menjaga kehormatan budaya Nusantara untuk generasi mendatang.',
                ],
                'mission' => [
                    'label' => 'Misi', 'type' => 'textarea', 'rows' => 4, 'max' => 1500,
                    'default' => "Menghadirkan koleksi yang asli dan terkurasi.\nMemberikan penjelasan yang jujur dan edukatif.\nMelayani kolektor dengan ramah, amanah, dan profesional.",
                    'help' => 'Satu baris = satu butir misi.',
                ],
            ],
        ],
    ],

    /* ==================================================================
     * BERANDA  (menu: Beranda)
     * ================================================================== */
    'home' => [
        'title' => 'Teks Beranda',
        'intro' => 'Ubah tulisan yang tampil di halaman depan website.',
        'sections' => [
            'Bagian Utama (Hero)' => [
                'hero_eyebrow' => [
                    'label' => 'Teks Kecil di Atas Judul', 'type' => 'text', 'max' => 80,
                    'default' => 'Galeri Pusaka & Barang Antik',
                ],
                'hero_title' => [
                    'label' => 'Judul Besar', 'type' => 'text', 'max' => 140,
                    'default' => 'Pusaka Berpamor Emas, Warisan Kejayaan Nusantara',
                ],
                'hero_subtitle' => [
                    'label' => 'Penjelasan Singkat', 'type' => 'textarea', 'rows' => 3, 'max' => 400,
                    'default' => 'Jelajahi koleksi keris, tombak, dan benda antik pilihan yang dikurasi dengan cermat — lengkap dengan kisah, pamor, dan keaslian yang dapat dipertanggungjawabkan.',
                ],
                'hero_button_text' => [
                    'label' => 'Tulisan Tombol Utama', 'type' => 'text', 'max' => 40,
                    'default' => 'Jelajahi Koleksi',
                ],
                'hero_button2_text' => [
                    'label' => 'Tulisan Tombol Kedua', 'type' => 'text', 'max' => 40,
                    'default' => 'Hubungi Kami',
                    'help' => 'Jika nomor WhatsApp sudah diisi, tombol ini membuka WhatsApp. Jika belum, membuka halaman Kontak.',
                ],
            ],
            'Judul Bagian' => [
                'categories_title' => [
                    'label' => 'Judul Bagian Kategori', 'type' => 'text', 'max' => 80,
                    'default' => 'Jelajahi Kategori',
                ],
                'featured_title' => [
                    'label' => 'Judul Koleksi Pilihan', 'type' => 'text', 'max' => 80,
                    'default' => 'Koleksi Pilihan',
                ],
                'featured_subtitle' => [
                    'label' => 'Penjelasan Koleksi Pilihan', 'type' => 'text', 'max' => 200,
                    'default' => 'Pusaka dan barang antik terbaik untuk para pecinta dan kolektor.',
                ],
                'about_home_title' => [
                    'label' => 'Judul Bagian Tentang (di Beranda)', 'type' => 'text', 'max' => 80,
                    'default' => 'Tentang Galeri Kami',
                ],
            ],
            'Keunggulan Toko (3 kotak)' => [
                'feature1_title' => ['label' => 'Keunggulan 1 — Judul', 'type' => 'text', 'max' => 50, 'default' => 'Keaslian Terjamin'],
                'feature1_text'  => ['label' => 'Keunggulan 1 — Penjelasan', 'type' => 'textarea', 'rows' => 2, 'max' => 250,
                    'default' => 'Setiap pusaka dijelaskan secara jujur: asal-usul, dhapur, pamor, dan kondisinya.'],
                'feature2_title' => ['label' => 'Keunggulan 2 — Judul', 'type' => 'text', 'max' => 50, 'default' => 'Kurasi Penuh Makna'],
                'feature2_text'  => ['label' => 'Keunggulan 2 — Penjelasan', 'type' => 'textarea', 'rows' => 2, 'max' => 250,
                    'default' => 'Kami memilih benda yang memiliki nilai sejarah, seni, dan filosofi — bukan sekadar benda lama.'],
                'feature3_title' => ['label' => 'Keunggulan 3 — Judul', 'type' => 'text', 'max' => 50, 'default' => 'Pengiriman Aman'],
                'feature3_text'  => ['label' => 'Keunggulan 3 — Penjelasan', 'type' => 'textarea', 'rows' => 2, 'max' => 250,
                    'default' => 'Dikemas khusus dan dikirim dengan asuransi ke seluruh Indonesia.'],
            ],
            'Ajakan di Bagian Bawah' => [
                'cta_title' => [
                    'label' => 'Judul Ajakan', 'type' => 'text', 'max' => 100,
                    'default' => 'Temukan Pusaka yang Berjodoh dengan Anda',
                ],
                'cta_text' => [
                    'label' => 'Teks Ajakan', 'type' => 'textarea', 'rows' => 2, 'max' => 250,
                    'default' => 'Konsultasikan kebutuhan koleksi Anda langsung dengan kami. Kami siap membantu.',
                ],
            ],
        ],
    ],

    /* ==================================================================
     * KONTAK & SOSIAL MEDIA  (menu: Kontak & Sosial)
     * ================================================================== */
    'contact' => [
        'title' => 'Kontak & Sosial Media',
        'intro' => 'Informasi yang tampil di halaman Kontak dan footer. Kosongkan isian yang tidak dipakai — otomatis tidak ditampilkan.',
        'sections' => [
            'Kontak Toko' => [
                'address' => [
                    'label' => 'Alamat', 'type' => 'textarea', 'rows' => 2, 'max' => 300, 'default' => '',
                ],
                'phone' => [
                    'label' => 'Nomor Telepon', 'type' => 'tel', 'default' => '',
                    'help' => 'Contoh: 0812 3456 7890',
                ],
                'whatsapp' => [
                    'label' => 'Nomor WhatsApp', 'type' => 'tel', 'default' => '',
                    'help' => 'Contoh: 081234567890 atau 6281234567890. Jika diisi, tombol WhatsApp otomatis muncul di website.',
                ],
                'whatsapp_greeting' => [
                    'label' => 'Kalimat Awal Chat WhatsApp', 'type' => 'text', 'max' => 200,
                    'default' => 'Halo, saya ingin bertanya tentang koleksi di galeri Anda.',
                ],
                'email' => [
                    'label' => 'Email', 'type' => 'email', 'default' => '',
                ],
                'opening_hours' => [
                    'label' => 'Jam Buka', 'type' => 'textarea', 'rows' => 2, 'max' => 200, 'default' => '',
                    'help' => 'Contoh: Senin – Sabtu, 09.00 – 17.00 WIB',
                ],
                'maps_embed' => [
                    'label' => 'Peta Google Maps (kode embed)', 'type' => 'textarea', 'rows' => 3, 'max' => 1500, 'default' => '',
                    'help' => 'Buka Google Maps → cari lokasi toko → Bagikan → Sematkan peta → Salin HTML → tempel di sini.',
                ],
            ],
            'Sosial Media' => [
                'instagram' => ['label' => 'Instagram (alamat lengkap)', 'type' => 'url', 'default' => '', 'help' => 'Contoh: https://instagram.com/namaanda'],
                'facebook'  => ['label' => 'Facebook (alamat lengkap)', 'type' => 'url', 'default' => ''],
                'tiktok'    => ['label' => 'TikTok (alamat lengkap)', 'type' => 'url', 'default' => ''],
                'youtube'   => ['label' => 'YouTube (alamat lengkap)', 'type' => 'url', 'default' => ''],
            ],
        ],
    ],

    /* ==================================================================
     * TAMPILAN & SEO  (menu: Tampilan & SEO)
     * ================================================================== */
    'site' => [
        'title' => 'Tampilan & SEO',
        'intro' => 'Logo, deskripsi untuk mesin pencari (Google), teks footer, dan jumlah produk per halaman.',
        'sections' => [
            'Logo & Footer' => [
                'site_logo' => [
                    'label' => 'Logo Website', 'type' => 'image', 'max_dim' => 600,
                    'default' => 'assets/img/logo.svg',
                    'help' => 'Dipakai di menu atas dan sebagai ikon tab browser. Disarankan PNG transparan berbentuk persegi.',
                ],
                'footer_text' => [
                    'label' => 'Teks Hak Cipta (Footer)', 'type' => 'text', 'max' => 150,
                    'default' => 'Seluruh hak cipta dilindungi.',
                ],
            ],
            'Pencarian Google (SEO)' => [
                'meta_description' => [
                    'label' => 'Deskripsi Website', 'type' => 'textarea', 'rows' => 3, 'max' => 300,
                    'default' => 'Galeri keris dan barang antik pilihan: keris berpamor, tombak, dan benda antik bersejarah dengan keaslian terjamin.',
                    'help' => 'Ringkasan yang tampil di hasil pencarian Google (sekitar 150 karakter).',
                ],
            ],
            'Katalog' => [
                'products_per_page' => [
                    'label' => 'Jumlah Produk per Halaman', 'type' => 'number', 'min' => 3, 'max_value' => 48,
                    'default' => '12',
                ],
            ],
        ],
    ],
];
