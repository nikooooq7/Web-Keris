<?php
/** LAYOUT ADMIN — menu samping + isi halaman. Variabel: $active (menu aktif), $pageTitle */
$shopName = setting('shop_name', 'Galeri Keris');
$logo = media_url(setting('site_logo')) ?: asset('img/logo.svg');
$user = Auth::user();
$unread = Message::unreadCount();
$active = $active ?? '';
$menu = function (string $key, string $path, string $label, string $icon, string $badge = '') use ($active) {
    return '<a href="' . e(url($path)) . '" class="' . ($active === $key ? 'active' : '') . '">' . icon($icon, 18) . ' <span>' . e($label) . '</span>' . $badge . '</a>';
};
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e(($pageTitle ?? 'Admin') . ' — Admin ' . $shopName) ?></title>
  <link rel="icon" href="<?= e($logo) ?>">
  <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body>
<div class="admin-shell">
  <aside class="admin-side" id="adminSide">
    <a class="side-brand" href="<?= e(url('admin')) ?>">
      <img src="<?= e($logo) ?>" alt="">
      <span><strong><?= e(str_limit($shopName, 26)) ?></strong><small>Panel Admin</small></span>
    </a>
    <nav class="side-nav">
      <?= $menu('dashboard', 'admin', 'Dashboard', 'home') ?>
      <div class="side-label">Koleksi</div>
      <?= $menu('produk', 'admin/produk', 'Produk', 'box') ?>
      <?= $menu('kategori', 'admin/kategori', 'Kategori', 'tag') ?>
      <div class="side-label">Pengaturan Website</div>
      <?= $menu('profil', 'admin/profil', 'Profil Toko', 'store') ?>
      <?= $menu('beranda', 'admin/beranda', 'Teks Beranda', 'edit') ?>
      <?= $menu('kontak', 'admin/kontak', 'Kontak & Sosial', 'phone') ?>
      <?= $menu('tampilan', 'admin/tampilan', 'Tampilan & SEO', 'gear') ?>
      <div class="side-label">Lainnya</div>
      <?= $menu('pesan', 'admin/pesan', 'Pesan Masuk', 'chat', $unread > 0 ? '<span class="badge rounded-pill">' . (int) $unread . '</span>' : '') ?>
      <?= $menu('akun', 'admin/akun', 'Akun Admin', 'user') ?>
    </nav>
    <div class="side-foot">
      <a class="btn btn-sm btn-outline-gold mb-2" href="<?= e(url('')) ?>" target="_blank" rel="noopener"><?= icon('external', 15) ?> Lihat Website</a>
      <form method="post" action="<?= e(url('admin/logout')) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-soft" type="submit"><?= icon('logout', 15) ?> Keluar</button></form>
    </div>
  </aside>
  <div class="side-backdrop" id="sideBackdrop"></div>

  <div class="admin-main">
    <header class="admin-top">
      <div class="d-flex align-items-center gap-2">
        <button class="menu-btn" id="menuBtn" type="button" aria-label="Menu"><?= icon('menu', 22) ?></button>
        <h1><?= e($pageTitle ?? '') ?></h1>
      </div>
      <span class="text-muted small d-none d-sm-inline">Halo, <strong><?= e($user['display_name'] ?? $user['username'] ?? '') ?></strong></span>
    </header>

    <div class="admin-content">
      <?php foreach (Flash::all() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert">
          <?= e($f['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      <?php endforeach; ?>
      <?= $content ?>
    </div>
  </div>
</div>
<script src="<?= e(asset('vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('js/admin.js')) ?>"></script>
</body>
</html>
