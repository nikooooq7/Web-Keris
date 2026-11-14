<?php
/** PROFIL KAMI — semua isi diatur dari Panel Admin → Profil Toko */
$pageTitle = 'Profil Kami';
$heroBg = media_url(setting('gallery_background')) ?: asset('img/bg-hero.svg');
$photo = media_url(setting('profile_photo')) ?: asset('img/logo.svg');
$owner = trim((string) setting('owner_name'));
$about = trim((string) setting('about_text'));
$history = trim((string) setting('history'));
$vision = trim((string) setting('vision'));
$missions = array_filter(array_map('trim', preg_split('/\R/', (string) setting('mission'))));
?>
<section class="page-hero" style="background-image:url('<?= e($heroBg) ?>'); padding:6rem 0 4rem">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Beranda</a></li><li class="breadcrumb-item active">Profil Kami</li></ol></nav>
    <h1><?= e(setting('shop_name')) ?></h1>
    <p><?= e(setting('tagline')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5">
        <div class="about-photo"><img src="<?= e($photo) ?>" alt="<?= e($owner !== '' ? $owner : setting('shop_name')) ?>"></div>
        <?php if ($owner !== ''): ?>
          <div class="text-center mt-4"><h2 class="h4 mb-0"><?= e($owner) ?></h2><small class="text-muted">Pemilik / Kurator</small></div>
        <?php endif; ?>
      </div>
      <div class="col-lg-7">
        <div class="ornament justify-content-start"><i></i></div>
        <h2 class="mb-3">Tentang Kami</h2>
        <div class="rich"><?= format_text($about) ?></div>
      </div>
    </div>
  </div>
</section>

<?php if ($history !== ''): ?>
<section class="section section-alt">
  <div class="container"><div class="row justify-content-center"><div class="col-lg-9">
    <div class="section-head mb-4"><div class="ornament"><i></i></div><h2>Sejarah Kami</h2></div>
    <div class="rich"><?= format_text($history) ?></div>
  </div></div></div>
</section>
<?php endif; ?>

<?php if ($vision !== '' || $missions): ?>
<section class="section">
  <div class="container">
    <div class="row g-4">
      <?php if ($vision !== ''): ?>
      <div class="col-lg-6"><div class="panel h-100"><h2 class="h4 mb-3">Visi</h2><div class="rich mb-0"><?= format_text($vision) ?></div></div></div>
      <?php endif; ?>
      <?php if ($missions): ?>
      <div class="col-lg-6"><div class="panel h-100"><h2 class="h4 mb-3">Misi</h2>
        <ul class="mission-list"><?php foreach ($missions as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul>
      </div></div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<img class="divider-keris" src="<?= e(asset('img/keris-divider.svg')) ?>" alt="" aria-hidden="true">

<section class="cta">
  <div class="container">
    <h2><?= e(setting('cta_title')) ?></h2>
    <p><?= e(setting('cta_text')) ?></p>
    <a href="<?= e(url('kontak')) ?>" class="btn btn-gold">Hubungi Kami</a>
  </div>
</section>
