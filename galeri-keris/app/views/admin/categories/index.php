<div class="row g-4">
  <div class="col-lg-4">
    <div class="card"><div class="card-body">
      <h2 class="section-title">Tambah Kategori</h2>
      <form method="post" action="<?= e(url('admin/kategori/tambah')) ?>">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label" for="name">Nama Kategori *</label><input class="form-control" id="name" name="name" maxlength="100" required value="<?= e(old('name')) ?>" placeholder="Contoh: Keris"></div>
        <div class="mb-3"><label class="form-label" for="description">Keterangan singkat</label><input class="form-control" id="description" name="description" maxlength="255" value="<?= e(old('description')) ?>"></div>
        <div class="mb-3"><label class="form-label" for="sort_order">Urutan tampil</label><input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= e(old('sort_order', '0')) ?>"><div class="form-text">Angka kecil tampil lebih dulu.</div></div>
        <button class="btn btn-gold w-100" type="submit"><?= icon('plus', 14) ?> Tambah</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-8">
    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead><tr><th>Nama</th><th>Produk</th><th>Urutan</th><th class="text-end">Aksi</th></tr></thead>
          <tbody>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td><strong><?= e($c['name']) ?></strong><?php if ($c['description'] !== ''): ?><br><small class="text-muted"><?= e($c['description']) ?></small><?php endif; ?></td>
              <td><?= (int) $c['product_count'] ?></td>
              <td><?= (int) $c['sort_order'] ?></td>
              <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-gold" href="<?= e(url('admin/kategori/' . $c['id'] . '/edit')) ?>"><?= icon('edit', 15) ?> Ubah</a>
                <form method="post" action="<?= e(url('admin/kategori/' . $c['id'] . '/hapus')) ?>" class="d-inline" data-confirm="Hapus kategori ini? Produk di dalamnya tidak ikut terhapus, hanya menjadi tanpa kategori.">
                  <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit"><?= icon('trash', 15) ?></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$categories): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
