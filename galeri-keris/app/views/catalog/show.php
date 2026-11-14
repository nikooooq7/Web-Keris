<?php
$pageTitle = $product['name'];
$metaDescription = str_limit($product['description'], 155);
$images = $media['images'];
$videos = $media['videos'];
$firstImg = $images ? upload_url($images[0]['file_path']) : asset('img/placeholder-product.svg');
$ogImage = $images ? absolute_url('uploads/' . $images[0]['file_path']) : '';
list($stLabel, $stClass) = product_status($product['status']);
$yt = youtube_id($product['video_url']);
$waMsg = 'Halo, saya tertarik dengan "' . $product['name'] . '". Apakah masih tersedia? ' . absolute_url('koleksi/' . $product['slug']);
$waLink = wa_link(setting('whatsapp'), $waMsg);
$specs = [
    'Kategori'    => $product['category_name'] ?? '',
    'Asal Daerah' => $product['origin'],
    'Era / Tahun' => $product['era'],
    'Bahan'       => $product['material'],
    'Ukuran'      => $product['dimension'],
    'Kondisi'     => $product['condition_note'],
];
$heroBg = media_url(setting('gallery_background')) ?: asset('img/bg-hero.svg');
?>
<section class="page-hero" style="background-image:url('<?= e($heroBg) ?>'); padding:2.5rem 0 1.8rem">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Beranda</a></li>
      <li class="breadcrumb-item"><a href="<?= e(url('koleksi')) ?>">Koleksi</a></li>
      <?php if (!empty($product['category_name'])): ?><li class="breadcrumb-item"><a href="<?= e(url('koleksi?kategori=' . $product['category_slug'])) ?>"><?= e($product['category_name']) ?></a></li><?php endif; ?>
      <li class="breadcrumb-item active"><?= e(str_limit($product['name'], 40)) ?></li>
    </ol></nav>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (Auth::check()): ?>
      <div class="alert alert-warning py-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>Anda sedang melihat sebagai admin.<?= !$product['is_active'] ? ' (Produk ini disembunyikan dari pengunjung.)' : '' ?></span>
        <a class="btn btn-sm btn-outline-gold py-1" href="<?= e(url('admin/produk/' . $product['id'] . '/edit')) ?>">Ubah produk ini</a>
      </div>
    <?php endif; ?>

    <div class="row g-5">
      <div class="col-lg-7">
        <div class="gallery-main"><img id="galleryMain" src="<?= e($firstImg) ?>" alt="<?= e($product['name']) ?>"></div>
        <?php if (count($images) > 1): ?>
          <div class="gallery-thumbs">
            <?php foreach ($images as $i => $img): ?>
              <button type="button" class="<?= $i === 0 ? 'active' : '' ?>" data-gallery-thumb data-full="<?= e(upload_url($img['file_path'])) ?>" aria-label="Foto <?= $i + 1 ?>">
                <img src="<?= e(upload_url($img['file_path'])) ?>" alt="" loading="lazy">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-5">
        <?php if (!empty($product['category_name'])): ?><span class="product-cat"><?= e($product['category_name']) ?></span><?php endif; ?>
        <h1 class="h2 mt-1 mb-3"><?= e($product['name']) ?></h1>
        <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
          <span class="price-tag"><?= $product['status'] === 'terjual' ? 'Terjual' : e(rupiah($product['price'])) ?></span>
          <span class="status-badge status-<?= e($stClass) ?>" style="position:static"><?= e($stLabel) ?></span>
        </div>

        <table class="spec-table">
          <?php foreach ($specs as $label => $val): if (trim((string) $val) === '') { continue; } ?>
            <tr><th><?= e($label) ?></th><td><?= e($val) ?></td></tr>
          <?php endforeach; ?>
        </table>

        <div class="d-grid gap-2 mt-4">
          <?php if ($product['status'] !== 'terjual'): ?>
            <?php if ($waLink): ?>
              <a class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener noreferrer" href="<?= e($waLink) ?>"><?= icon('whatsapp', 20) ?> Tanya / Pesan via WhatsApp</a>
            <?php endif; ?>
            <a class="btn btn-gold" href="<?= e(url('kontak')) ?>">Kirim Pesan</a>
          <?php else: ?>
            <div class="alert alert-secondary mb-0">Koleksi ini sudah terjual. Hubungi kami untuk mencari yang serupa.</div>
            <a class="btn btn-outline-gold" href="<?= e(url('kontak')) ?>">Hubungi Kami</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if (trim((string) $product['description']) !== ''): ?>
      <div class="row mt-5"><div class="col-lg-10">
        <div class="ornament justify-content-start"><i></i></div>
        <h2 class="h4 mb-3">Deskripsi</h2>
        <div class="rich"><?= format_text($product['description']) ?></div>
      </div></div>
    <?php endif; ?>

    <?php if ($yt || $videos): ?>
      <div class="mt-5">
        <div class="ornament justify-content-start"><i></i></div>
        <h2 class="h4 mb-3">Video</h2>
        <div class="row g-4">
          <?php if ($yt): ?>
            <div class="col-lg-6"><div class="video-frame">
              <iframe src="https://www.youtube-nocookie.com/embed/<?= e($yt) ?>" title="Video <?= e($product['name']) ?>" loading="lazy" allowfullscreen
                      allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div></div>
          <?php endif; ?>
          <?php foreach ($videos as $v): ?>
            <div class="col-lg-6"><div class="video-frame">
              <video controls preload="metadata" playsinline>
                <source src="<?= e(upload_url($v['file_path'])) ?>" type="<?= e(video_mime($v['file_path'])) ?>">
                Browser Anda tidak mendukung pemutar video.
              </video>
            </div></div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($related): ?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head"><div class="ornament"><i></i></div><h2>Koleksi Lainnya</h2></div>
    <div class="row g-4">
      <?php foreach ($related as $p): ?>
        <div class="col-sm-6 col-lg-3"><?php include APP_PATH . '/views/partials/product_card.php'; ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
