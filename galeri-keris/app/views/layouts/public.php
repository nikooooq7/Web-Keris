<?php
/**
 * LAYOUT PUBLIK — kerangka semua halaman website (menu atas + footer).
 * Variabel opsional dari halaman: $pageTitle, $metaDescription
 */
$shopName = setting('shop_name', 'Galeri Keris');
$logo = media_url(setting('site_logo')) ?: asset('img/logo.svg');
$title = !empty($pageTitle) ? $pageTitle . ' — ' . $shopName : $shopName . ' — ' . setting('tagline');
$desc = !empty($metaDescription) ? $metaDescription : setting('meta_description');
$waLink = wa_link(setting('whatsapp'), (string) setting('whatsapp_greeting'));
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($desc) ?>">
  <meta name="theme-color" content="#0c0804">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($desc) ?>">
  <meta property="og:type" content="website">
  <?php if (!empty($ogImage)): ?><meta property="og:image" content="<?= e($ogImage) ?>"><?php endif; ?>
  <link rel="icon" href="<?= e($logo) ?>">
  <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>

<header class="position-sticky top-0" style="z-index:1030">
  <nav class="navbar navbar-expand-lg site-nav">
    <div class="container">
      <a class="navbar-brand brand m-0" href="<?= e(url('')) ?>">
        <img src="<?= e($logo) ?>" alt="Logo <?= e($shopName) ?>">
        <span>
          <span class="brand-name"><?= e($shopName) ?></span>
          <span class="brand-tag d-none d-sm-block"><?= e(str_limit(setting('tagline'), 48)) ?></span>
        </span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu"
              aria-controls="mainMenu" aria-expanded="false" aria-label="Buka menu">
        <?= icon('menu', 26) ?>
      </button>
      <div class="collapse navbar-collapse" id="mainMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link <?= nav_active('/') ?>" href="<?= e(url('')) ?>">Beranda</a></li>
          <li class="nav-item"><a class="nav-link <?= nav_active('/koleksi') ?>" href="<?= e(url('koleksi')) ?>">Koleksi</a></li>
          <li class="nav-item"><a class="nav-link <?= nav_active('/profil') ?>" href="<?= e(url('profil')) ?>">Profil Kami</a></li>
          <li class="nav-item"><a class="nav-link <?= nav_active('/kontak') ?>" href="<?= e(url('kontak')) ?>">Kontak</a></li>
          <?php if (Auth::check()): ?>
            <li class="nav-item ms-lg-2"><a class="btn btn-outline-gold btn-sm py-1 px-3" href="<?= e(url('admin')) ?>">Panel Admin</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>

<?php $flashes = Flash::all(); if ($flashes): ?>
<div class="flash-wrap">
  <?php foreach ($flashes as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert">
      <?= e($f['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<main>
<?= $content ?>
</main>

<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <a class="brand mb-3" href="<?= e(url('')) ?>">
          <img src="<?= e($logo) ?>" alt="">
          <span class="brand-name"><?= e($shopName) ?></span>
        </a>
        <p class="small text-muted mb-3"><?= e(str_limit(setting('tagline'), 140)) ?></p>
        <div class="social">
          <?php foreach (['instagram', 'facebook', 'tiktok', 'youtube'] as $net): $u = trim((string) setting($net)); if ($u !== ''): ?>
            <a href="<?= e($u) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e(ucfirst($net)) ?>"><?= icon($net, 18) ?></a>
          <?php endif; endforeach; ?>
          <?php if ($waLink): ?><a href="<?= e($waLink) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><?= icon('whatsapp', 18) ?></a><?php endif; ?>
        </div>
      </div>
      <div class="col-6 col-lg-3 offset-lg-1">
        <h4>Menu</h4>
        <ul>
          <li><a href="<?= e(url('')) ?>">Beranda</a></li>
          <li><a href="<?= e(url('koleksi')) ?>">Koleksi</a></li>
          <li><a href="<?= e(url('profil')) ?>">Profil Kami</a></li>
          <li><a href="<?= e(url('kontak')) ?>">Kontak</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-4">
        <h4>Hubungi Kami</h4>
        <ul class="small">
          <?php if (trim((string) setting('address')) !== ''): ?><li class="d-flex gap-2"><?= icon('pin', 16) ?> <span><?= nl2br(e(setting('address'))) ?></span></li><?php endif; ?>
          <?php if (trim((string) setting('phone')) !== ''): ?><li class="d-flex gap-2"><?= icon('phone', 16) ?> <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li><?php endif; ?>
          <?php if (trim((string) setting('email')) !== ''): ?><li class="d-flex gap-2"><?= icon('mail', 16) ?> <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif; ?>
          <?php if (trim((string) setting('opening_hours')) !== ''): ?><li class="d-flex gap-2"><?= icon('clock', 16) ?> <span><?= nl2br(e(setting('opening_hours'))) ?></span></li><?php endif; ?>
          <?php if (trim((string) setting('address') . (string) setting('phone') . (string) setting('email') . (string) setting('opening_hours')) === ''): ?>
            <li class="text-muted">Informasi kontak belum diisi.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
      <span>&copy; <?= date('Y') ?> <?= e($shopName) ?>. <?= e(setting('footer_text')) ?></span>
      <a href="<?= e(url('admin')) ?>">Masuk Admin</a>
    </div>
  </div>
</footer>

<?php if ($waLink): ?>
<a class="wa-float" href="<?= e($waLink) ?>" target="_blank" rel="noopener noreferrer" aria-label="Chat WhatsApp"><?= icon('whatsapp', 30) ?></a>
<?php endif; ?>

<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0 text-center">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button>
        <img id="photoModalImg" src="" alt="" class="img-fluid" style="max-height:88vh">
      </div>
    </div>
  </div>
</div>

<script src="<?= e(asset('vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
