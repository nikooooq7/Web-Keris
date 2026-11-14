<div class="card">
  <div class="card-body border-bottom">
    <form method="get" action="<?= e(url('admin/produk')) ?>" class="row g-2 align-items-end">
      <div class="col-md-4"><label class="form-label" for="q">Cari</label><input type="search" class="form-control" id="q" name="q" value="<?= e($q) ?>" placeholder="Nama produk..."></div>
      <div class="col-md-3"><label class="form-label" for="kategori">Kategori</label>
        <select class="form-select" id="kategori" name="kategori"><option value="">Semua</option>
          <?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= selected($cat, $c['id']) ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select></div>
      <div class="col-md-2"><label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status"><option value="">Semua</option>
          <?php foreach (Product::STATUSES as $k => $l): ?><option value="<?= e($k) ?>" <?= selected($status, $k) ?>><?= e($l) ?></option><?php endforeach; ?>
        </select></div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-soft flex-fill" type="submit">Saring</button>
        <a class="btn btn-gold" href="<?= e(url('admin/produk/tambah')) ?>"><?= icon('plus', 14) ?> Tambah Produk</a>
      </div>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-hover">
      <thead><tr><th>Foto</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($result['items'] as $p): list($stLabel, $stClass) = product_status($p['status']); ?>
        <tr>
          <td><img class="thumb-sm" src="<?= e(!empty($p['cover']) ? upload_url($p['cover']) : asset('img/placeholder-product.svg')) ?>" alt=""></td>
          <td>
            <a class="fw-semibold" href="<?= e(url('admin/produk/' . $p['id'] . '/edit')) ?>"><?= e(str_limit($p['name'], 60)) ?></a>
            <?php if ($p['is_featured']): ?><span class="badge text-bg-warning ms-1">Pilihan</span><?php endif; ?>
            <?php if (!$p['is_active']): ?><span class="badge-status hidden ms-1">Disembunyikan</span><?php endif; ?>
            <br><small class="text-muted"><?= (int) $p['views'] ?>x dilihat</small>
          </td>
          <td><?= e($p['category_name'] ?? '—') ?></td>
          <td class="text-nowrap"><?= e(rupiah($p['price'])) ?></td>
          <td><span class="badge-status <?= e($stClass) ?>"><?= e($stLabel) ?></span></td>
          <td class="text-end text-nowrap">
            <?php if ($p['is_active']): ?><a class="btn btn-sm btn-soft" target="_blank" rel="noopener" href="<?= e(url('koleksi/' . $p['slug'])) ?>" title="Lihat di website"><?= icon('eye', 15) ?></a><?php endif; ?>
            <a class="btn btn-sm btn-outline-gold" href="<?= e(url('admin/produk/' . $p['id'] . '/edit')) ?>"><?= icon('edit', 15) ?> Ubah</a>
            <form method="post" action="<?= e(url('admin/produk/' . $p['id'] . '/hapus')) ?>" class="d-inline" data-confirm="Hapus produk ini beserta semua foto dan videonya? Tindakan ini tidak bisa dibatalkan.">
              <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit" title="Hapus"><?= icon('trash', 15) ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?>
        <tr><td colspan="6" class="text-center text-muted py-5">Belum ada produk. <a href="<?= e(url('admin/produk/tambah')) ?>">Tambahkan produk pertama Anda</a>.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3"><?= pagination_html($result['page'], $result['pages'], 'admin/produk', ['q' => $q, 'status' => $status, 'kategori' => $cat]) ?></div>
<p class="text-muted small text-center"><?= (int) $result['total'] ?> produk</p>
