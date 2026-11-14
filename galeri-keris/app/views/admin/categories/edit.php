<div class="row"><div class="col-lg-6">
  <div class="card"><div class="card-body">
    <form method="post" action="<?= e(url('admin/kategori/' . $cat['id'] . '/edit')) ?>">
      <?= csrf_field() ?>
      <div class="mb-3"><label class="form-label" for="name">Nama Kategori *</label><input class="form-control" id="name" name="name" maxlength="100" required value="<?= e(old('name', $cat['name'])) ?>"></div>
      <div class="mb-3"><label class="form-label" for="description">Keterangan singkat</label><input class="form-control" id="description" name="description" maxlength="255" value="<?= e(old('description', $cat['description'])) ?>"></div>
      <div class="mb-4"><label class="form-label" for="sort_order">Urutan tampil</label><input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= e(old('sort_order', $cat['sort_order'])) ?>"></div>
      <button class="btn btn-gold" type="submit">Simpan</button>
      <a class="btn btn-soft" href="<?= e(url('admin/kategori')) ?>">Batal</a>
    </form>
  </div></div>
</div></div>
