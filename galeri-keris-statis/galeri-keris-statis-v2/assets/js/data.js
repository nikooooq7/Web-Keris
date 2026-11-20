/* ==========================================================================
   DATA.JS  —  SATU-SATUNYA FILE YANG PERLU ANDA EDIT UNTUK MENGUBAH ISI SITUS
   --------------------------------------------------------------------------
   Semua teks, kontak, foto profil, kategori, dan produk ada di sini.
   Header, footer, halaman, filter, dan halaman detail produk dibuat
   OTOMATIS dari data ini oleh assets/js/site.js. Tidak perlu menyentuh HTML.

   Aturan menulis: setiap baris diakhiri koma, teks diapit tanda kutip "...".
   ========================================================================== */

window.SITE = {
  nama: "Galeri Keris Nusantara",
  tagline: "Pusaka Adiluhung, Warisan Kejayaan Nusantara",
  deskripsi: "Galeri keris dan barang antik pilihan: keris berpamor, tombak, dan benda pusaka bersejarah.",

  /* ---- KONTAK (tampil di footer, halaman Kontak, tombol WhatsApp) ---- */
  kontak: {
    alamat: "DTC Mall Lt 3 No 104 Surabaya",
    telepon: "081 2311 5679",
    whatsapp: "081 2311 5679",      // nomor WhatsApp (boleh sama dengan telepon)
    email: "aguskeris66@gmail.com",
    jamBuka: "Setiap Hari, 10.00 - 17.00 Wib"
  },

  /* ---- FOTO ---- (ganti nama file di sini jika ingin foto lain) ---- */
  foto: {
    profil: "assets/img/profil.jpg",          // foto profil (lebar) -> Profil Kami & Beranda
    profilBulat: "assets/img/profil-square.jpg", // foto profil (persegi) -> header & footer
    showcase: "assets/img/koleksi-keris.jpg", // foto pajangan di Beranda
    og: "assets/img/og-image.jpg"
  },

  /* ---- MEDIA SOSIAL (kosongkan "" jika belum ada) ---- */
  sosial: { instagram: "", facebook: "", tiktok: "", youtube: "" },

  /* ---- TEKS HALAMAN ---- */
  beranda: {
    eyebrow: "Galeri Pusaka & Barang Antik",
    judul: "Pusaka Berpamor Emas, Warisan Kejayaan Nusantara",
    lead: "Jelajahi koleksi keris, tombak, dan benda pusaka pilihan yang dikurasi dengan cermat, lengkap dengan kisah, pamor, dan keaslian yang dapat dipertanggungjawabkan.",
    jumlahPilihan: 4,
    judulPajangan: "Ragam Pusaka dalam Satu Meja",
    teksPajangan: "Keris dengan beragam dhapur, pamor, dan gaya ganjar-wilah menanti untuk dilihat langsung di galeri kami."
  },
  profil: {
    judul: "Tentang Kami",
    paragraf: [
      "Galeri Keris Nusantara adalah rumah bagi pusaka-pusaka pilihan: keris, tombak, serta benda-benda antik lainnya yang dikurasi dengan teliti.",
      "Setiap benda kami sertai penjelasan yang jujur mengenai asal-usul, pamor, dhapur, bahan, dan kondisinya, sehingga para pecinta maupun kolektor dapat memiliki dengan tenang dan bangga."
    ],
    sejarahJudul: "Sejarah Kami",
    sejarah: [
      "Galeri ini dirintis dari kecintaan terhadap warisan leluhur: keris, tombak, dan benda-benda antik yang menyimpan cerita zaman. Dari koleksi kecil bersama sahabat sesama pecinta pusaka, galeri tumbuh menjadi ruang pamer yang terbuka bagi siapa saja yang ingin belajar dan memiliki.",
      "Kami percaya setiap pusaka layak dirawat, dijelaskan dengan jujur, dan berpindah ke tangan yang tepat."
    ],
    visi: "Menjadi galeri pusaka dan barang antik yang terpercaya, menjaga kehormatan budaya Nusantara untuk generasi mendatang.",
    misi: [
      "Menghadirkan koleksi yang asli dan terkurasi.",
      "Memberikan penjelasan yang jujur dan edukatif.",
      "Melayani kolektor dengan ramah, amanah, dan profesional."
    ],
    ajakanJudul: "Temukan Pusaka yang Berjodoh dengan Anda",
    ajakanTeks: "Konsultasikan kebutuhan koleksi Anda langsung dengan kami. Kami siap membantu."
  },
  kontakHalaman: {
    judul: "Hubungi Kami",
    sub: "Kami senang menjawab pertanyaan Anda tentang koleksi dan pusaka."
  }
};

/* ---- KATEGORI: id dipakai oleh produk di bawah ---- */
window.KATEGORI = [
  { id: "keris",  nama: "Keris" },
  { id: "tombak", nama: "Tombak & Senjata Pusaka" }
];

/* ---- STATUS yang tersedia ---- */
window.STATUS = {
  tersedia: "Tersedia",
  dipesan: "Dipesan",
  terjual: "Terjual"
};

/* ==========================================================================
   PRODUK  —  untuk menambah produk: salin satu blok { ... }, ganti isinya,
   taruh foto di folder uploads/products/ (dan versi kecil di thumbs/).
   - harga: angka (contoh 12500000) atau null jika "Hubungi Kami"
   - foto: daftar nama file; foto pertama jadi foto utama
   - pilihan: boleh dikosongkan "" agar baris tidak tampil di halaman detail
   ========================================================================== */
window.PRODUK = [
  {
    slug: "keris-pusaka-warangka-kayu",
    nama: "Keris Pusaka Warangka Kayu Jati",
    kategori: "keris",
    status: "tersedia",
    harga: null,
    unggulan: true,
    asal: "", era: "", bahan: "Besi, baja, dan pamor; warangka kayu", ukuran: "", kondisi: "Terawat",
    ringkas: "Keris lengkap dengan warangka kayu berpelat dan hulu ukir.",
    deskripsi: [
      "Keris lengkap dengan warangka kayu berpelat serta hulu (jejeran) berukir. Bilah dan ganjar tampil terawat dengan sentuhan warna emas pada bagian pangkal.",
      "Untuk keterangan dhapur, pamor, tangguh, dan ukuran bilah, silakan hubungi kami; kami jelaskan apa adanya."
    ],
    foto: ["keris-luk7-hitam.jpg"]
  },
  {
    slug: "keris-warangka-gayaman-ukir-merah-1",
    nama: "Keris Warangka Gayaman Ukir Merah I",
    kategori: "keris",
    status: "tersedia",
    harga: null,
    unggulan: true,
    asal: "", era: "", bahan: "Besi, baja, dan pamor; warangka kayu berukir", ukuran: "", kondisi: "Terawat",
    ringkas: "Warangka gayaman berukir motif merah dengan ganjar berhias emas.",
    deskripsi: [
      "Keris dengan warangka gayaman yang diukir halus bermotif merah dan putih, berpadu dengan ganjar berhias ukiran kuning keemasan serta hulu kayu berukir.",
      "Tanyakan kepada kami untuk keterangan dhapur, pamor, dan ukuran bilah."
    ],
    foto: ["keris-warangka-ukir-1.jpg"]
  },
  {
    slug: "keris-warangka-gayaman-ukir-merah-2",
    nama: "Keris Warangka Gayaman Ukir Merah II",
    kategori: "keris",
    status: "tersedia",
    harga: null,
    unggulan: true,
    asal: "", era: "", bahan: "Besi, baja, dan pamor; warangka kayu berukir", ukuran: "", kondisi: "Terawat",
    ringkas: "Warangka gayaman berukir merah dengan hulu kayu dan ganjar bersepuh emas.",
    deskripsi: [
      "Pusaka lain dengan warangka gayaman berukir merah dan putih, ganjar bersepuh emas, dan hulu kayu yang kokoh.",
      "Tanyakan kepada kami untuk keterangan dhapur, pamor, dan ukuran bilah."
    ],
    foto: ["keris-warangka-ukir-2.jpg"]
  },
  {
    slug: "keris-luk-pusaka-sepuh-emas",
    nama: "Keris Luk Pusaka Bersepuh Emas",
    kategori: "keris",
    status: "tersedia",
    harga: null,
    unggulan: true,
    asal: "", era: "", bahan: "Besi, baja, pamor; sepuh emas pada warangka dan bilah", ukuran: "", kondisi: "Terawat",
    ringkas: "Keris berluk dengan warangka berlapis emas yang berkilau dan hulu kayu.",
    deskripsi: [
      "Keris berluk dengan warangka yang berlapis warna emas, ganjar berhias, serta hulu kayu dengan cincin perak. Kilau emasnya hadir kuat di bawah cahaya.",
      "Silakan hubungi kami untuk melihat langsung dan mengetahui keterangan dhapur serta pamor bilahnya."
    ],
    foto: ["keris-luk-emas.jpg"]
  },
  {
    slug: "tombak-trisula-berlapis-emas",
    nama: "Tombak Trisula Berlapis Emas",
    kategori: "tombak",
    status: "tersedia",
    harga: null,
    unggulan: true,
    asal: "", era: "", bahan: "Besi dan baja; lapis (tinatah) emas", ukuran: "", kondisi: "Terawat",
    ringkas: "Tombak trisula bercabang tiga dengan hiasan emas yang melingkar di sepanjang bilah.",
    deskripsi: [
      "Tombak berujung trisula (bercabang tiga) dengan bilah berliku yang dihiasi sepuh dan tatahan emas. Tampil gagah, cocok untuk kolektor pusaka.",
      "Hubungi kami untuk keterangan lengkap tentang tangkai (landeyan), ukuran, dan kondisinya."
    ],
    foto: ["trisula-emas.jpg"]
  }
];
