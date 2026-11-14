<?php
$pageTitle = 'Koleksi';
$heroBg = media_url(setting('gallery_background')) ?: asset('img/bg-hero.svg');
$query = ['q' => $q, 'kategori' => $catSlug, 'status' => $status];
$activeCatName = '';
foreach ($categories as $c) {
    if ($c['slug'] === $catSlug) {
        $activeCatName = $c['name'];
    }
}
?>
<section class="page-hero" style="background-image:url('<?= e($heroBg) ?>')">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Beranda</a></li><li class="breadcrumb-item active">Koleksi</li></ol></nav>
    <h1><?= $activeCatName !== '' ? e($activeCatName) : 'Koleksi Pusaka & Barang Antik' ?></h1>
    <p>Temukan keris, tombak, dan benda antik pilihan kami.</p>
  </div>
</section>

<section class="section">
  <div class="container">

    <div class="filter-bar">
      <form method="get" action="<?= e(url('koleksi')) ?>" class="row g-2 align-items-end">
        <?php if ($catSlug !== ''): ?><input type="hidden" name="kategori" value="<?= e($catSlug) ?>"><?php endif; ?>
        <div class="col-md-6">
          <label class="form-label" for="q">Cari koleksi</label>
          <input type="search" class="form-control" id="q" name="q" value="<?= e($q) ?>" placeholder="Nama, asal daerah, bahan, atau era...">
        </div>
        <div class="col-md-3">
          <label class="form-label" for="status">Status</label>
          <select class="form-select" id="status" name="status">
            <option value="">Semua</option>
            <?php foreach (Product::STATUSES as $k => $label): ?>
              <option value="<?= e($k) ?>" <?= selected($status, $k) ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-gold flex-fill" type="submit"><?= icon('search', 16) ?> Cari</button>
          <?php if ($q !== '' || $status !== '' || $catSlug !== ''): ?><a class="btn btn-outline-gold" href="<?= e(url('koleksi')) ?>">Reset</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="mb-4">
      <a class="chip <?= $catSlug === '' ? 'active' : '' ?>" href="<?= e(url('koleksi' . ($q !== '' ? '?q=' . rawurlencode($q) : ''))) ?>">Semua</a>
      <?php foreach ($categories as $c): ?>
        <a class="chip <?= $catSlug === $c['slug'] ? 'active' : '' ?>" href="<?= e(url('koleksi?kategori=' . $c['slug'] . ($q !== '' ? '&q=' . rawurlencode($q) : ''))) ?>">
          <?= e($c['name']) ?> <small>(<?= (int) $c['product_count'] ?>)</small>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($result['items']): ?>
      <p class="text-muted small mb-3"><?= (int) $result['total'] ?> koleksi ditemukan</p>
      <div class="row g-4">
        <?php foreach ($result['items'] as $p): ?>
          <div class="col-sm-6 col-lg-4 col-xl-3"><?php include APP_PATH . '/views/partials/product_card.php'; ?></div>
        <?php endforeach; ?>
      </div>
      <div class="mt-5"><?= pagination_html($result['page'], $result['pages'], 'koleksi', $query) ?></div>
    <?php else: ?>
      <div class="empty-state">
        <?= icon('search', 48) ?>
        <h3 class="mt-3">Belum ada koleksi yang cocok</h3>
        <p>Coba kata kunci lain, atau <a href="<?= e(url('koleksi')) ?>">lihat semua koleksi</a>.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
