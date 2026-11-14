<?php
/** KONTAK — alamat, telepon, dll. diatur dari Panel Admin → Kontak & Sosial */
$pageTitle = 'Kontak';
$heroBg = media_url(setting('gallery_background')) ?: asset('img/bg-hero.svg');
$map = maps_embed_src(setting('maps_embed'));
$waLink = wa_link(setting('whatsapp'), (string) setting('whatsapp_greeting'));
$address = trim((string) setting('address'));
$phone = trim((string) setting('phone'));
$email = trim((string) setting('email'));
$hours = trim((string) setting('opening_hours'));
?>
<section class="page-hero" style="background-image:url('<?= e($heroBg) ?>')">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Beranda</a></li><li class="breadcrumb-item active">Kontak</li></ol></nav>
    <h1>Hubungi Kami</h1>
    <p>Kami senang menjawab pertanyaan Anda tentang koleksi dan pusaka.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5">
        <h2 class="h4 mb-4">Informasi Toko</h2>
        <?php if ($address !== ''): ?><div class="contact-line"><?= icon('pin', 22) ?><div><strong>Alamat</strong><?= nl2br(e($address)) ?></div></div><?php endif; ?>
        <?php if ($phone !== ''): ?><div class="contact-line"><?= icon('phone', 22) ?><div><strong>Telepon</strong><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a></div></div><?php endif; ?>
        <?php if ($email !== ''): ?><div class="contact-line"><?= icon('mail', 22) ?><div><strong>Email</strong><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></div></div><?php endif; ?>
        <?php if ($hours !== ''): ?><div class="contact-line"><?= icon('clock', 22) ?><div><strong>Jam Buka</strong><?= nl2br(e($hours)) ?></div></div><?php endif; ?>
        <?php if ($address . $phone . $email . $hours === ''): ?><p class="text-muted">Informasi kontak belum diisi. Silakan gunakan formulir di samping.</p><?php endif; ?>
        <?php if ($waLink): ?><a class="btn btn-whatsapp mt-2" target="_blank" rel="noopener noreferrer" href="<?= e($waLink) ?>"><?= icon('whatsapp', 18) ?> Chat WhatsApp</a><?php endif; ?>
      </div>

      <div class="col-lg-7">
        <div class="panel">
          <h2 class="h4 mb-4">Kirim Pesan</h2>
          <form method="post" action="<?= e(url('kontak')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="hp-field" aria-hidden="true"><label>Jangan diisi<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label" for="name">Nama *</label><input class="form-control" id="name" name="name" maxlength="100" required value="<?= e(old('name')) ?>"></div>
              <div class="col-md-6"><label class="form-label" for="phone">Telepon / WhatsApp</label><input class="form-control" id="phone" name="phone" maxlength="40" value="<?= e(old('phone')) ?>"></div>
              <div class="col-md-6"><label class="form-label" for="email">Email</label><input type="email" class="form-control" id="email" name="email" maxlength="150" value="<?= e(old('email')) ?>"></div>
              <div class="col-md-6"><label class="form-label" for="subject">Subjek</label><input class="form-control" id="subject" name="subject" maxlength="150" value="<?= e(old('subject')) ?>"></div>
              <div class="col-12"><label class="form-label" for="body">Pesan *</label><textarea class="form-control" id="body" name="body" rows="5" maxlength="3000" required><?= e(old('body')) ?></textarea></div>
              <div class="col-12"><small class="text-muted">Isi email atau nomor telepon agar kami bisa membalas.</small></div>
              <div class="col-12"><button class="btn btn-gold" type="submit">Kirim Pesan</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php if ($map): ?>
      <div class="mt-5">
        <h2 class="h4 mb-3">Lokasi Kami</h2>
        <div class="map-frame"><iframe src="<?= e($map) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi" allowfullscreen></iframe></div>
      </div>
    <?php endif; ?>
  </div>
</section>
