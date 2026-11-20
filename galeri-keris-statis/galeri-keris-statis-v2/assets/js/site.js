/* ==========================================================================
   SITE.JS — mesin yang membangun semua halaman dari data.js
   Anda biasanya TIDAK perlu mengubah file ini. Ubah isi situs di data.js.
   ========================================================================== */
(function () {
  'use strict';

  var S = window.SITE, K = window.KATEGORI, P = window.PRODUK, ST = window.STATUS;
  var page = (document.getElementById('main') || {}).dataset || {};
  page = page.page || 'beranda';

  /* ---------- alat bantu ---------- */
  function esc(t) {
    return String(t == null ? '' : t).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function $(sel, root) { return (root || document).querySelector(sel); }
  function $$(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }
  function rupiah(n) { return 'Rp ' + Number(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
  function harga(p) { return p.harga ? rupiah(p.harga) : 'Hubungi Kami'; }
  function kat(id) { return K.filter(function (k) { return k.id === id; })[0] || { id: id, nama: id }; }
  function fotoUtama(p, thumb) { return 'uploads/products/' + (thumb ? 'thumbs/' : '') + p.foto[0]; }
  function cari(slug) { return P.filter(function (p) { return p.slug === slug; })[0]; }
  function params() { return new URLSearchParams(location.search); }
  function waNomor() {
    var n = String(S.kontak.whatsapp || S.kontak.telepon).replace(/\D/g, '');
    if (n.charAt(0) === '0') n = '62' + n.slice(1);
    return n;
  }
  function waLink(teks) { return 'https://wa.me/' + waNomor() + '?text=' + encodeURIComponent(teks); }
  function telLink() { return 'tel:+' + waNomor(); }
  function nl2br(t) { return esc(t).replace(/\n/g, '<br>'); }
  function paragraf(arr) { return arr.map(function (t) { return '<p>' + esc(t) + '</p>'; }).join(''); }
  function setMeta(judul, deskripsi) {
    document.title = judul + ' — ' + S.nama;
    var d = $('meta[name="description"]');
    if (d && deskripsi) d.setAttribute('content', deskripsi);
  }

  /* ---------- ikon ---------- */
  var IKON = {
    pin: 'M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 4.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z',
    phone: 'M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1l-2.3 2.2z',
    mail: 'M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm1.5 2L12 12.5 19.5 7h-15zM4 9.2V17h16V9.2l-8 5.8-8-5.8z',
    clock: 'M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20zm-1 5v6l4.5 2.7.8-1.3-3.8-2.2V7H11z',
    menu: 'M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z',
    tag: 'M3 3h8l10 10-8 8L3 11V3zm4 2.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z',
    search: 'M10 2a8 8 0 0 1 6.3 12.9l5.4 5.4-1.4 1.4-5.4-5.4A8 8 0 1 1 10 2zm0 2a6 6 0 1 0 0 12 6 6 0 0 0 0-12z',
    wa: 'M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.2 14.2c-.2.6-1.300 1.200-1.800 1.200-.5.100-1 .2-3.300-.7-2.800-1.100-4.600-4-4.700-4.200-.1-.2-1.100-1.500-1.100-2.800s.7-2 1-2.300c.2-.3.5-.3.700-.3h.5c.2 0 .4 0 .6.500l.8 1.900c.1.200.1.400 0 .6l-.4.600c-.2.200-.3.400-.1.700.2.300.8 1.300 1.700 2.100 1.100 1 2.100 1.300 2.400 1.500.3.100.5.100.7-.1l.9-1.100c.2-.3.4-.2.700-.1l1.800.9c.3.100.5.200.5.300.1.200.1.800-.1 1.400z'
  };
  function ikon(n, s) {
    s = s || 18;
    return '<svg class="icon" width="' + s + '" height="' + s + '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="' + IKON[n] + '"/></svg>';
  }

  /* ---------- komponen ---------- */
  var MENU = [
    ['beranda', 'index.html', 'Beranda'],
    ['koleksi', 'koleksi.html', 'Koleksi'],
    ['profil', 'profil.html', 'Profil Kami'],
    ['kontak', 'kontak.html', 'Kontak']
  ];
  var aktif = page === 'produk' ? 'koleksi' : page;

  function header() {
    return '<header class="position-sticky top-0" style="z-index:1030"><nav class="navbar navbar-expand-lg site-nav"><div class="container">' +
      '<a class="navbar-brand brand m-0" href="index.html">' +
      '<img src="' + esc(S.foto.profilBulat) + '" alt="Foto profil ' + esc(S.nama) + '">' +
      '<span><span class="brand-name">' + esc(S.nama) + '</span><span class="brand-tag d-none d-sm-block">' + esc(S.tagline) + '</span></span></a>' +
      '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Buka menu">' + ikon('menu', 26) + '</button>' +
      '<div class="collapse navbar-collapse" id="mainMenu"><ul class="navbar-nav ms-auto align-items-lg-center">' +
      MENU.map(function (m) {
        return '<li class="nav-item"><a class="nav-link' + (m[0] === aktif ? ' active' : '') + '" href="' + m[1] + '">' + m[2] + '</a></li>';
      }).join('') + '</ul></div></div></nav></header>';
  }

  function footer() {
    var k = S.kontak, sosial = '';
    Object.keys(S.sosial || {}).forEach(function (n) {
      if (S.sosial[n]) sosial += '<a href="' + esc(S.sosial[n]) + '" target="_blank" rel="noopener">' + esc(n.charAt(0).toUpperCase() + n.slice(1)) + '</a> ';
    });
    return '<footer class="site-footer"><div class="container"><div class="row g-4">' +
      '<div class="col-lg-4"><a class="brand mb-3" href="index.html"><img src="' + esc(S.foto.profilBulat) + '" alt=""><span class="brand-name">' + esc(S.nama) + '</span></a>' +
      '<p class="small text-muted mb-3">' + esc(S.tagline) + '</p><div class="social">' + sosial + '</div></div>' +
      '<div class="col-6 col-lg-3 offset-lg-1"><h4>Menu</h4><ul>' +
      MENU.map(function (m) { return '<li><a href="' + m[1] + '">' + m[2] + '</a></li>'; }).join('') + '</ul></div>' +
      '<div class="col-6 col-lg-4"><h4>Hubungi Kami</h4><ul class="small">' +
      '<li class="d-flex gap-2">' + ikon('pin', 16) + ' <span>' + nl2br(k.alamat) + '</span></li>' +
      '<li class="d-flex gap-2">' + ikon('phone', 16) + ' <a href="' + telLink() + '">' + esc(k.telepon) + '</a></li>' +
      '<li class="d-flex gap-2">' + ikon('mail', 16) + ' <a href="mailto:' + esc(k.email) + '">' + esc(k.email) + '</a></li>' +
      '<li class="d-flex gap-2">' + ikon('clock', 16) + ' <span>' + esc(k.jamBuka) + '</span></li></ul></div></div>' +
      '<div class="footer-bottom d-flex flex-wrap justify-content-between gap-2"><span>&copy; ' + new Date().getFullYear() + ' ' + esc(S.nama) + '. Hak cipta dilindungi.</span></div>' +
      '</div></footer>' +
      '<a class="wa-float" href="' + waLink('Halo ' + S.nama + ', saya ingin bertanya tentang koleksi pusaka.') + '" target="_blank" rel="noopener" aria-label="Chat WhatsApp">' + ikon('wa', 28) + '</a>';
  }

  function kartu(p) {
    var lencana = p.status && p.status !== 'tersedia'
      ? '<span class="status-badge status-' + esc(p.status === 'dipesan' ? 'reserved' : 'sold') + '">' + esc(ST[p.status] || p.status) + '</span>' : '';
    var meta = [p.asal, p.era].filter(Boolean).join(' · ') || p.ringkas || '';
    return '<a class="product-card ' + (p.status === 'terjual' ? 'is-sold' : '') + '" href="produk.html?p=' + encodeURIComponent(p.slug) + '">' +
      '<div class="product-thumb">' + lencana + '<img src="' + esc(fotoUtama(p, true)) + '" alt="' + esc(p.nama) + '" loading="lazy"></div>' +
      '<div class="product-body"><span class="product-cat">' + esc(kat(p.kategori).nama) + '</span>' +
      '<h3 class="product-title">' + esc(p.nama) + '</h3>' +
      (meta ? '<div class="product-meta">' + esc(meta) + '</div>' : '') +
      '<div class="product-price">' + esc(harga(p)) + '</div></div></a>';
  }
  function grid(list, kolom) {
    return '<div class="row g-4 justify-content-center">' + list.map(function (p) {
      return '<div class="' + (kolom || 'col-sm-6 col-lg-3') + '">' + kartu(p) + '</div>';
    }).join('') + '</div>';
  }
  function ornamen(h, sub) {
    return '<div class="section-head"><div class="ornament"><i></i></div><h2>' + esc(h) + '</h2>' + (sub ? '<p>' + esc(sub) + '</p>' : '') + '</div>';
  }
  function pageHero(judul, sub, crumbs) {
    return '<section class="page-hero" style="background-image:url(\'assets/img/bg-hero.svg\')"><div class="container">' +
      '<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.html">Beranda</a></li>' +
      (crumbs || '') + '<li class="breadcrumb-item active">' + esc(judul) + '</li></ol></nav>' +
      '<h1>' + esc(judul) + '</h1>' + (sub ? '<p>' + esc(sub) + '</p>' : '') + '</div></section>';
  }
  function cta(h, t) {
    return '<section class="cta"><div class="container"><h2>' + esc(h) + '</h2><p>' + esc(t) + '</p>' +
      '<a href="kontak.html" class="btn btn-gold">Hubungi Kami</a></div></section>';
  }
  var DIVIDER = '<img class="divider-keris" src="assets/img/keris-divider.svg" alt="" aria-hidden="true">';

  /* ---------- halaman ---------- */
  function halBeranda() {
    var B = S.beranda;
    var unggulan = P.filter(function (p) { return p.unggulan; });
    if (!unggulan.length) unggulan = P;
    unggulan = unggulan.slice(0, Math.max(B.jumlahPilihan, 8));
    var kategori = K.map(function (k) {
      var n = P.filter(function (p) { return p.kategori === k.id; }).length;
      return n ? '<div class="col-6 col-md-4 col-lg-3"><a class="cat-card" href="koleksi.html?kategori=' + esc(k.id) + '"><div class="cat-icon">' + ikon('tag', 30) + '</div><h3>' + esc(k.nama) + '</h3><small>' + n + ' koleksi</small></a></div>' : '';
    }).join('');
    return '<section class="hero" style="background-image:url(\'assets/img/bg-hero.svg\')"><div class="container"><div class="row align-items-center g-4">' +
      '<div class="col-lg-7"><div class="hero-eyebrow">' + esc(B.eyebrow) + '</div><h1>' + esc(B.judul) + '</h1><p class="lead">' + esc(B.lead) + '</p>' +
      '<div class="d-flex flex-wrap gap-3 mt-4 justify-content-center justify-content-lg-start"><a href="koleksi.html" class="btn btn-gold">Jelajahi Koleksi</a><a href="kontak.html" class="btn btn-outline-gold">Hubungi Kami</a></div></div>' +
      '<div class="col-lg-5 d-none d-lg-block"><div class="hero-keris"><img src="assets/img/keris-hero.svg" alt="Keris berlapis emas dengan pamor"></div></div></div></div></section>' +

      '<section class="section"><div class="container">' + ornamen('Jelajahi Kategori') + '<div class="row g-4 justify-content-center">' + kategori + '</div></div></section>' +
      DIVIDER +
      '<section class="section section-pattern"><div class="container">' + ornamen('Koleksi Pilihan', 'Pusaka dan barang antik terbaik untuk para pecinta dan kolektor.') + grid(unggulan) +
      '<div class="text-center mt-5"><a href="koleksi.html" class="btn btn-outline-gold">Lihat Semua Koleksi</a></div></div></section>' +

      '<section class="section section-alt"><div class="container"><div class="row g-5 align-items-center">' +
      '<div class="col-lg-6"><div class="ornament justify-content-start"><i></i></div><h2 class="mb-3">' + esc(B.judulPajangan) + '</h2><p class="text-muted">' + esc(B.teksPajangan) + '</p><a href="koleksi.html" class="btn btn-gold mt-2">Lihat Koleksi</a></div>' +
      '<div class="col-lg-6"><div class="showcase"><img src="' + esc(S.foto.showcase) + '" alt="Deretan keris di atas meja pameran" loading="lazy"></div></div></div></div></section>' +

      '<section class="section"><div class="container"><div class="row g-5 align-items-center">' +
      '<div class="col-lg-5"><div class="about-photo"><img src="' + esc(S.foto.profil) + '" alt="Foto profil ' + esc(S.nama) + '"></div></div>' +
      '<div class="col-lg-7"><div class="ornament justify-content-start"><i></i></div><h2 class="mb-3">Tentang ' + esc(S.nama) + '</h2><div class="rich">' + paragraf(S.profil.paragraf) + '</div><a href="profil.html" class="btn btn-outline-gold mt-2">Profil Kami</a></div></div></div></section>' +
      cta(S.profil.ajakanJudul, S.profil.ajakanTeks);
  }

  function halKoleksi() {
    var q = params();
    var chips = '<a class="chip" data-kat="" href="koleksi.html">Semua <small>(' + P.length + ')</small></a>' + K.map(function (k) {
      var n = P.filter(function (p) { return p.kategori === k.id; }).length;
      return n ? '<a class="chip" data-kat="' + esc(k.id) + '" href="koleksi.html?kategori=' + esc(k.id) + '">' + esc(k.nama) + ' <small>(' + n + ')</small></a>' : '';
    }).join('');
    return pageHero('Koleksi Pusaka & Barang Antik', 'Temukan keris, tombak, dan benda antik pilihan kami.') +
      '<section class="section"><div class="container">' +
      '<div class="filter-bar"><form id="filterForm" class="row g-2 align-items-end" onsubmit="return false">' +
      '<div class="col-md-5"><label class="form-label" for="q">Cari koleksi</label><input type="search" class="form-control" id="q" placeholder="Nama, bahan, atau keterangan..." value="' + esc(q.get('q') || '') + '"></div>' +
      '<div class="col-md-3"><label class="form-label" for="status">Status</label><select class="form-select" id="status"><option value="">Semua</option>' +
      Object.keys(ST).map(function (k) { return '<option value="' + k + '">' + esc(ST[k]) + '</option>'; }).join('') + '</select></div>' +
      '<div class="col-md-3"><label class="form-label" for="urut">Urutkan</label><select class="form-select" id="urut"><option value="">Terbaru ditambahkan</option><option value="nama">Nama A–Z</option><option value="murah">Harga terendah</option><option value="mahal">Harga tertinggi</option></select></div>' +
      '<div class="col-md-1 d-grid"><button class="btn btn-outline-gold" type="reset" id="resetBtn" title="Hapus filter" aria-label="Hapus filter">&times;</button></div></form></div>' +
      '<div class="mb-4" id="chips">' + chips + '</div>' +
      '<p class="text-muted small" id="resultCount" role="status" aria-live="polite"></p>' +
      '<div id="hasil"></div>' +
      '<div class="empty-state" id="kosong" hidden><h3 class="h5">Koleksi tidak ditemukan</h3><p>Coba kata kunci lain atau hapus filter.</p><a href="koleksi.html" class="btn btn-outline-gold">Lihat semua</a></div>' +
      '</div></section>';
  }

  function aktifkanKoleksi() {
    var q = params();
    var el = { q: $('#q'), st: $('#status'), ur: $('#urut'), hasil: $('#hasil'), jml: $('#resultCount'), kosong: $('#kosong') };
    var kategori = q.get('kategori') || '';
    el.st.value = q.get('status') || '';
    el.ur.value = q.get('urut') || '';

    function render() {
      var kata = el.q.value.trim().toLowerCase();
      var list = P.map(function (p, i) { p._i = i; return p; }).filter(function (p) {
        if (kategori && p.kategori !== kategori) return false;
        if (el.st.value && p.status !== el.st.value) return false;
        if (kata) {
          var teks = [p.nama, p.asal, p.era, p.bahan, p.ringkas, p.ukuran].join(' ').toLowerCase();
          if (teks.indexOf(kata) < 0) return false;
        }
        return true;
      });
      var u = el.ur.value;
      list.sort(function (a, b) {
        if (u === 'nama') return a.nama.localeCompare(b.nama, 'id');
        if (u === 'murah') return (a.harga || Infinity) - (b.harga || Infinity);
        if (u === 'mahal') return (b.harga || 0) - (a.harga || 0);
        return b._i - a._i;
      });
      el.hasil.innerHTML = list.length ? grid(list) : '';
      el.kosong.hidden = list.length > 0;
      el.jml.textContent = list.length + ' koleksi ditemukan' + (kategori ? ' dalam kategori ' + kat(kategori).nama : '');
      $$('#chips .chip').forEach(function (c) { c.classList.toggle('active', c.getAttribute('data-kat') === kategori); });
      // simpan filter di alamat halaman supaya bisa dibagikan
      var u2 = new URLSearchParams();
      if (el.q.value.trim()) u2.set('q', el.q.value.trim());
      if (kategori) u2.set('kategori', kategori);
      if (el.st.value) u2.set('status', el.st.value);
      if (u) u2.set('urut', u);
      try { history.replaceState(null, '', location.pathname + (u2.toString() ? '?' + u2 : '')); } catch (e) { /* file:// */ }
    }
    el.q.addEventListener('input', render);
    el.st.addEventListener('change', render);
    el.ur.addEventListener('change', render);
    $('#chips').addEventListener('click', function (e) {
      var c = e.target.closest('.chip'); if (!c) return;
      e.preventDefault(); kategori = c.getAttribute('data-kat'); render();
    });
    $('#resetBtn').addEventListener('click', function () {
      setTimeout(function () { kategori = ''; el.q.value = ''; el.st.value = ''; el.ur.value = ''; render(); }, 0);
    });
    render();
    setMeta('Koleksi', 'Daftar koleksi keris, tombak, dan benda antik ' + S.nama + '.');
  }

  function halProduk() {
    var p = cari(params().get('p'));
    if (!p) {
      setMeta('Produk tidak ditemukan');
      return pageHero('Produk tidak ditemukan', 'Produk yang Anda cari tidak tersedia.') +
        '<section class="section"><div class="container empty-state"><a href="koleksi.html" class="btn btn-gold">Kembali ke Koleksi</a></div></section>';
    }
    var k = kat(p.kategori);
    var spek = [['Kategori', k.nama], ['Asal Daerah', p.asal], ['Era / Tahun', p.era], ['Bahan', p.bahan], ['Ukuran', p.ukuran], ['Kondisi', p.kondisi]]
      .filter(function (r) { return r[1]; })
      .map(function (r) { return '<tr><th>' + r[0] + '</th><td>' + esc(r[1]) + '</td></tr>'; }).join('');
    var thumbs = p.foto.length > 1 ? '<div class="gallery-thumbs">' + p.foto.map(function (f, i) {
      return '<button type="button" class="' + (i ? '' : 'active') + '" data-gallery-thumb data-full="uploads/products/' + esc(f) + '" aria-label="Foto ' + (i + 1) + '"><img src="uploads/products/thumbs/' + esc(f) + '" alt="" loading="lazy"></button>';
    }).join('') + '</div>' : '';
    var pesan = 'Halo ' + S.nama + ', saya tertarik dengan "' + p.nama + '". Apakah masih tersedia?';
    var lain = P.filter(function (x) { return x.slug !== p.slug && x.kategori === p.kategori; });
    if (lain.length < 4) lain = lain.concat(P.filter(function (x) { return x.slug !== p.slug && x.kategori !== p.kategori; }));
    lain = lain.slice(0, 4);
    setMeta(p.nama, p.ringkas);
    return '<section class="page-hero" style="background-image:url(\'assets/img/bg-hero.svg\'); padding:2.5rem 0 1.8rem"><div class="container"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">' +
      '<li class="breadcrumb-item"><a href="index.html">Beranda</a></li><li class="breadcrumb-item"><a href="koleksi.html">Koleksi</a></li>' +
      '<li class="breadcrumb-item"><a href="koleksi.html?kategori=' + esc(k.id) + '">' + esc(k.nama) + '</a></li><li class="breadcrumb-item active">' + esc(p.nama) + '</li></ol></nav></div></section>' +
      '<section class="section"><div class="container"><div class="row g-5">' +
      '<div class="col-lg-7"><div class="gallery-main"><img id="galleryMain" src="uploads/products/' + esc(p.foto[0]) + '" alt="' + esc(p.nama) + '" style="cursor:zoom-in"></div>' + thumbs + '</div>' +
      '<div class="col-lg-5"><span class="product-cat">' + esc(k.nama) + '</span><h1 class="h2 mt-1 mb-3">' + esc(p.nama) + '</h1>' +
      '<div class="d-flex flex-wrap align-items-center gap-3 mb-2"><span class="price-tag">' + esc(harga(p)) + '</span>' +
      '<span class="status-badge status-' + (p.status === 'dipesan' ? 'reserved' : p.status === 'terjual' ? 'sold' : 'available') + '" style="position:static">' + esc(ST[p.status] || p.status) + '</span></div>' +
      '<table class="spec-table">' + spek + '</table>' +
      '<div class="d-grid gap-2 mt-4"><a class="btn btn-gold" href="' + waLink(pesan) + '" target="_blank" rel="noopener">' + ikon('wa', 18) + ' Tanya via WhatsApp</a>' +
      '<a class="btn btn-outline-gold" href="kontak.html?subjek=' + encodeURIComponent(p.nama) + '">Kirim Pesan</a></div></div></div>' +
      '<div class="row mt-5"><div class="col-lg-10"><div class="ornament justify-content-start"><i></i></div><h2 class="h4 mb-3">Deskripsi</h2><div class="rich">' + paragraf(p.deskripsi) + '</div></div></div></div></section>' +
      (lain.length ? '<section class="section section-alt"><div class="container">' + ornamen('Koleksi Lainnya') + grid(lain) + '</div></section>' : '');
  }

  function aktifkanGaleri() {
    var main = $('#galleryMain'); if (!main) return;
    $$('[data-gallery-thumb]').forEach(function (b) {
      b.addEventListener('click', function () {
        main.src = b.getAttribute('data-full');
        $$('[data-gallery-thumb]').forEach(function (x) { x.classList.remove('active'); });
        b.classList.add('active');
      });
    });
    var host = document.createElement('div');
    host.innerHTML = '<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content bg-transparent border-0"><div class="modal-body p-0 text-center">' +
      '<button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button><img id="photoModalImg" src="" alt="" class="img-fluid" style="max-height:88vh"></div></div></div></div>';
    document.body.appendChild(host.firstChild);
    var modal = window.bootstrap ? new bootstrap.Modal($('#photoModal')) : null;
    main.addEventListener('click', function () {
      if (!modal) return;
      $('#photoModalImg').src = main.src; modal.show();
    });
  }

  function halProfil() {
    var R = S.profil;
    setMeta('Profil Kami', S.deskripsi);
    return pageHero('Profil Kami', S.tagline) +
      '<section class="section"><div class="container"><div class="row g-5 align-items-center">' +
      '<div class="col-lg-5"><div class="about-photo"><img src="' + esc(S.foto.profil) + '" alt="Foto profil ' + esc(S.nama) + '"></div></div>' +
      '<div class="col-lg-7"><div class="ornament justify-content-start"><i></i></div><h2 class="mb-3">' + esc(R.judul) + '</h2><div class="rich">' + paragraf(R.paragraf) + '</div></div></div></div></section>' +
      '<section class="section section-alt"><div class="container"><div class="row justify-content-center"><div class="col-lg-9"><div class="section-head mb-4"><div class="ornament"><i></i></div><h2>' + esc(R.sejarahJudul) + '</h2></div><div class="rich">' + paragraf(R.sejarah) + '</div></div></div></div></section>' +
      '<section class="section"><div class="container"><div class="row g-4">' +
      '<div class="col-lg-6"><div class="panel h-100"><h2 class="h4 mb-3">Visi</h2><div class="rich mb-0"><p>' + esc(R.visi) + '</p></div></div></div>' +
      '<div class="col-lg-6"><div class="panel h-100"><h2 class="h4 mb-3">Misi</h2><ul class="mission-list">' + R.misi.map(function (m) { return '<li>' + esc(m) + '</li>'; }).join('') + '</ul></div></div></div></div></section>' +
      DIVIDER + cta(R.ajakanJudul, R.ajakanTeks);
  }

  function halKontak() {
    var k = S.kontak, C = S.kontakHalaman;
    setMeta('Kontak', C.sub);
    var peta = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(k.alamat);
    function baris(i, label, isi) { return '<div class="contact-line">' + ikon(i, 22) + '<div><strong>' + label + '</strong>' + isi + '</div></div>'; }
    return pageHero(C.judul, C.sub) +
      '<section class="section"><div class="container"><div class="row g-5"><div class="col-lg-5"><h2 class="h4 mb-4">Informasi Toko</h2>' +
      baris('pin', 'Alamat', nl2br(k.alamat) + '<br><a href="' + peta + '" target="_blank" rel="noopener">Lihat di Google Maps</a>') +
      baris('phone', 'Telepon / WhatsApp', '<a href="' + telLink() + '">' + esc(k.telepon) + '</a>') +
      baris('mail', 'Email', '<a href="mailto:' + esc(k.email) + '">' + esc(k.email) + '</a>') +
      baris('clock', 'Jam Buka', esc(k.jamBuka)) + '</div>' +
      '<div class="col-lg-7"><div class="panel"><h2 class="h4 mb-4">Kirim Pesan</h2>' +
      '<form id="formKontak" novalidate><div class="row g-3">' +
      '<div class="col-md-6"><label class="form-label" for="f-nama">Nama *</label><input class="form-control" id="f-nama" maxlength="100" required></div>' +
      '<div class="col-md-6"><label class="form-label" for="f-hp">Telepon / WhatsApp</label><input class="form-control" id="f-hp" maxlength="40" inputmode="tel"></div>' +
      '<div class="col-12"><label class="form-label" for="f-subjek">Subjek</label><input class="form-control" id="f-subjek" maxlength="150" value="' + esc(params().get('subjek') || '') + '"></div>' +
      '<div class="col-12"><label class="form-label" for="f-pesan">Pesan *</label><textarea class="form-control" id="f-pesan" rows="5" maxlength="3000" required></textarea></div>' +
      '<div class="col-12"><div id="pesanForm" role="alert"></div><small class="text-muted d-block mb-2">Pesan akan dibuka di aplikasi WhatsApp atau email Anda, lalu tinggal Anda kirim.</small></div>' +
      '<div class="col-12 d-flex flex-wrap gap-2"><button class="btn btn-gold" type="submit" data-via="wa">' + ikon('wa', 18) + ' Kirim via WhatsApp</button><button class="btn btn-outline-gold" type="submit" data-via="email">' + ikon('mail', 18) + ' Kirim via Email</button></div>' +
      '</div></form></div></div></div></div></section>';
  }

  function aktifkanForm() {
    var f = $('#formKontak'); if (!f) return;
    var via = 'wa';
    $$('button[type=submit]', f).forEach(function (b) { b.addEventListener('click', function () { via = b.getAttribute('data-via'); }); });
    f.addEventListener('submit', function (e) {
      e.preventDefault();
      var nama = $('#f-nama').value.trim(), hp = $('#f-hp').value.trim(), sub = $('#f-subjek').value.trim(), msg = $('#f-pesan').value.trim();
      var box = $('#pesanForm');
      if (!nama || !msg) { box.className = 'alert alert-warning py-2'; box.textContent = 'Nama dan pesan wajib diisi.'; return; }
      var isi = 'Nama: ' + nama + (hp ? '\nTelepon: ' + hp : '') + (sub ? '\nSubjek: ' + sub : '') + '\n\n' + msg;
      box.className = 'alert alert-success py-2'; box.textContent = 'Membuka ' + (via === 'wa' ? 'WhatsApp' : 'aplikasi email') + '...';
      if (via === 'wa') window.open(waLink(isi), '_blank', 'noopener');
      else location.href = 'mailto:' + S.kontak.email + '?subject=' + encodeURIComponent(sub || 'Pesan dari website') + '&body=' + encodeURIComponent(isi);
    });
  }

  /* ---------- jalankan ---------- */
  var HALAMAN = { beranda: halBeranda, koleksi: halKoleksi, produk: halProduk, profil: halProfil, kontak: halKontak };

  function mulai() {
    var h = $('#site-header'), f = $('#site-footer'), m = $('#main');
    if (h) h.innerHTML = header();
    if (f) f.innerHTML = footer();
    if (m) m.innerHTML = (HALAMAN[page] || halBeranda)();
    if (page === 'beranda') setMeta('Pusaka Adiluhung', S.deskripsi);
    if (page === 'koleksi') aktifkanKoleksi();
    if (page === 'produk') aktifkanGaleri();
    if (page === 'kontak') aktifkanForm();
    // ikon tab & gambar pratinjau diambil dari data
    var ic = $('link[rel="icon"]'); if (ic) ic.setAttribute('href', 'assets/img/favicon.svg');
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mulai); else mulai();
})();
