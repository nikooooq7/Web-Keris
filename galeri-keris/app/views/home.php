<?php
/** BERANDA — teks-teks di sini diatur dari Panel Admin → Teks Beranda */
$heroBg = media_url(setting('gallery_background')) ?: asset('img/bg-hero.svg');
$waLink = wa_link(setting('whatsapp'), (string) setting('whatsapp_greeting'));
$about = trim((string) setting('about_text'));
$photo = media_url(setting('profile_photo'));
?>
<section class="hero" style="background-image:url('<?= e($heroBg) ?>')">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <div class="hero-eyebrow"><?= e(setting('hero_eyebrow')) ?></div>
        <h1><?= e(setting('hero_title')) ?></h1>
        <p class="lead"><?= e(setting('hero_subtitle')) ?></p>
        <div class="d-flex flex-wrap gap-3 mt-4 justify-content-center justify-content-lg-start">
          <a href="<?= e(url('koleksi')) ?>" class="btn btn-gold"><?= e(setting('hero_button_text')) ?></a>
          <?php if ($waLink): ?>
            <a href="<?= e($waLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-gold"><?= e(setting('hero_button2_text')) ?></a>
          <?php else: ?>
            <a href="<?= e(url('kontak')) ?>" class="btn btn-outline-gold"><?= e(setting('hero_button2_text')) ?></a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-block">
        <div class="hero-keris"><img src="<?= e(asset('img/kerisimg.png')) ?>" alt="Keris berlapis emas dengan pamor"></div>
      </div>
    </div>
  </div>
</section>

<?php if ($categories): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <div class="ornament"><i></i></div>
      <h2><?= e(setting('categories_title')) ?></h2>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach ($categories as $c): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <a class="cat-card" href="<?= e(url('koleksi?kategori=' . $c['slug'])) ?>">
            <div class="cat-icon"><?= icon('tag', 30) ?></div>
            <h3><?= e($c['name']) ?></h3>
            <small><?= (int) $c['product_count'] ?> koleksi</small>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<img class="divider-keris" src="<?= e(asset('img/keris-divider.svg')) ?>" alt="" aria-hidden="true">

<section class="section section-pattern">
  <div class="container">
    <div class="section-head">
      <div class="ornament"><i></i></div>
      <h2><?= e(setting('featured_title')) ?></h2>
      <p><?= e(setting('featured_subtitle')) ?></p>
    </div>
    <?php if ($featured): ?>
      <div class="row g-4 justify-content-center">
        <?php foreach ($featured as $p): ?>
          <div class="col-sm-6 col-lg-3"><?php include APP_PATH . '/views/partials/product_card.php'; ?></div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-5"><a href="<?= e(url('koleksi')) ?>" class="btn btn-outline-gold">Lihat Semua Koleksi</a></div>
    <?php else: ?>
      <div class="empty-state">Koleksi akan segera hadir.</div>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-4">
      <?php for ($i = 1; $i <= 3; $i++): $ic = ['', 'check', 'eye', 'box'][$i]; ?>
        <div class="col-md-4">
          <div class="feature">
            <div class="feature-icon"><?= icon($ic, 30) ?></div>
            <h3><?= e(setting('feature' . $i . '_title')) ?></h3>
            <p><?= e(setting('feature' . $i . '_text')) ?></p>
          </div>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<?php if ($about !== ''): ?>
<section class="section section-alt">
  <div class="container">
    <div class="row align-items-center g-5">
      <?php if ($photo): ?>
      <div class="col-lg-5"><div class="about-photo"><img src="<?= e($photo) ?>" alt="<?= e(setting('shop_name')) ?>" loading="lazy"></div></div>
      <?php endif; ?>
      <div class="<?= $photo ? 'col-lg-7' : 'col-lg-10 mx-auto' ?>">
        <div class="ornament justify-content-start"><i></i></div>
        <h2 class="mb-3"><?= e(setting('about_home_title')) ?></h2>
        <div class="rich"><?= format_text(str_limit($about, 700)) ?></div>
        <a href="<?= e(url('profil')) ?>" class="btn btn-outline-gold mt-2">Selengkapnya</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta">
  <div class="container">
    <h2><?= e(setting('cta_title')) ?></h2>
    <p><?= e(setting('cta_text')) ?></p>
    <?php if ($waLink): ?>
      <a href="<?= e($waLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-gold">Chat via WhatsApp</a>
    <?php else: ?>
      <a href="<?= e(url('kontak')) ?>" class="btn btn-gold">Hubungi Kami</a>
    <?php endif; ?>
  </div>
</section>
