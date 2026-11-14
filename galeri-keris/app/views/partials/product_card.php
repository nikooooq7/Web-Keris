<?php
/** Kartu produk. Dipakai di Beranda, Koleksi, dan Produk Terkait. Butuh variabel $p (data produk). */
list($stLabel, $stClass) = product_status($p['status']);
$cover = !empty($p['cover']) ? upload_url($p['cover']) : asset('img/placeholder-product.svg');
?>
<a class="product-card <?= $p['status'] === 'terjual' ? 'is-sold' : '' ?>" href="<?= e(url('koleksi/' . $p['slug'])) ?>">
  <div class="product-thumb">
    <?php if ($p['status'] !== 'tersedia'): ?><span class="status-badge status-<?= e($stClass) ?>"><?= e($stLabel) ?></span><?php endif; ?>
    <img src="<?= e($cover) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
  </div>
  <div class="product-body">
    <?php if (!empty($p['category_name'])): ?><span class="product-cat"><?= e($p['category_name']) ?></span><?php endif; ?>
    <h3 class="product-title"><?= e($p['name']) ?></h3>
    <?php if (!empty($p['origin']) || !empty($p['era'])): ?>
      <div class="product-meta"><?= e(trim($p['origin'] . ($p['origin'] && $p['era'] ? ' · ' : '') . $p['era'])) ?></div>
    <?php endif; ?>
    <div class="product-price"><?= $p['status'] === 'terjual' ? 'Terjual' : e(rupiah($p['price'])) ?></div>
  </div>
</a>
