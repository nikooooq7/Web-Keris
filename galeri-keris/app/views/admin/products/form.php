<?php
/** Form tambah / ubah produk. $product = null berarti tambah baru. */
$isEdit = $product !== null;
$action = $isEdit ? 'admin/produk/' . (int) $product['id'] . '/edit' : 'admin/produk/tambah';
$hasOld = Flash::hasOld();

// Nilai isian: dari isian sebelumnya (jika validasi gagal) -> data produk -> kosong
$val = function (string $key, $default = '') use ($product, $hasOld) {
    if ($hasOld) {
        return old($key, $default);
    }
    return $product[$key] ?? $default;
};
$flag = function (string $key, bool $default) use ($product, $hasOld) {
    if ($hasOld) {
        return Flash::old($key, null) !== null;
    }
    return $product ? !empty($product[$key]) : $default;
};
$priceVal = $hasOld ? old('price') : (isset($product['price']) && $product['price'] !== null ? number_format((int) $product['price'], 0, ',', '.') : '');

$limit = ini_to_bytes((string) ini_get('post_max_size'));
$upl = ini_to_bytes((string) ini_get('upload_max_filesize'));
$maxBytes = ($limit > 0 && $upl > 0) ? min($limit, $upl) : max($limit, $upl);
?>
<form method="post" action="<?= e(url($action)) ?>" enctype="multipart/form-data" novalidate>
  <?= csrf_field() ?>
  <div class="row g-4">

    <div class="col-lg-8">
      <div class="card mb-4"><div class="card-body">
        <h2 class="section-title">Informasi Produk</h2>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label" for="name">Nama Produk *</label>
            <input class="form-control" id="name" name="name" maxlength="200" required value="<?= e($val('name')) ?>" placeholder="Contoh: Keris Luk 7 Pamor Beras Wutah">
          </div>
          <div class="col-md-6">
            <label class="form-label" for="category_id">Kategori</label>
            <select class="form-select" id="category_id" name="category_id">
              <option value="">— Tanpa kategori —</option>
              <?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= selected($val('category_id'), $c['id']) ?>><?= e($c['name']) ?></option><?php endforeach; ?>
            </select>
            <div class="form-text">Kategori dikelola di menu <a href="<?= e(url('admin/kategori')) ?>">Kategori</a>.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="price">Harga (Rupiah)</label>
            <div class="input-group"><span class="input-group-text">Rp</span>
              <input class="form-control" id="price" name="price" inputmode="numeric" value="<?= e($priceVal) ?>" placeholder="12.500.000"></div>
            <div class="form-text">Kosongkan jika harga ingin ditampilkan sebagai "Hubungi Kami".</div>
          </div>
          <div class="col-12">
            <label class="form-label" for="description">Deskripsi / Penjelasan</label>
            <textarea class="form-control" id="description" name="description" rows="9" maxlength="20000" placeholder="Ceritakan tentang pusaka/barang ini: asal-usul, pamor, dhapur, kisah, keistimewaan..."><?= e($val('description')) ?></textarea>
            <div class="form-text">Tekan Enter dua kali untuk membuat paragraf baru.</div>
          </div>
        </div>
      </div></div>

      <div class="card mb-4"><div class="card-body">
        <h2 class="section-title">Spesifikasi</h2>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label" for="origin">Asal Daerah</label><input class="form-control" id="origin" name="origin" maxlength="150" value="<?= e($val('origin')) ?>" placeholder="Contoh: Jawa Tengah"></div>
          <div class="col-md-6"><label class="form-label" for="era">Era / Tahun</label><input class="form-control" id="era" name="era" maxlength="150" value="<?= e($val('era')) ?>" placeholder="Contoh: Abad ke-19"></div>
          <div class="col-md-6"><label class="form-label" for="material">Bahan</label><input class="form-control" id="material" name="material" maxlength="150" value="<?= e($val('material')) ?>" placeholder="Contoh: Besi, baja, nikel (pamor)"></div>
          <div class="col-md-6"><label class="form-label" for="dimension">Ukuran</label><input class="form-control" id="dimension" name="dimension" maxlength="150" value="<?= e($val('dimension')) ?>" placeholder="Contoh: Panjang bilah ± 36 cm"></div>
          <div class="col-12"><label class="form-label" for="condition_note">Kondisi</label><input class="form-control" id="condition_note" name="condition_note" maxlength="255" value="<?= e($val('condition_note')) ?>" placeholder="Contoh: Terawat, warangan baru"></div>
        </div>
      </div></div>

      <div class="card mb-4"><div class="card-body">
        <h2 class="section-title">Foto &amp; Video</h2>

        <?php if ($isEdit && ($media['images'] || $media['videos'])): ?>
          <?php if ($media['images']): ?>
            <p class="fw-semibold mb-2">Foto saat ini</p>
            <div class="media-grid mb-3">
              <?php foreach ($media['images'] as $i => $m): ?>
                <div class="media-item <?= $i === 0 ? 'cover' : '' ?>">
                  <img src="<?= e(upload_url($m['file_path'])) ?>" alt="">
                  <?php if ($i === 0): ?><div class="small fw-semibold text-center mt-1" style="color:var(--a-gold-dark)">★ Foto sampul</div>
                  <?php else: ?><label><input type="radio" name="cover_media" value="<?= (int) $m['id'] ?>"> Jadikan sampul</label><?php endif; ?>
                  <label class="text-danger"><input type="checkbox" name="delete_media[]" value="<?= (int) $m['id'] ?>"> Hapus foto</label>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if ($media['videos']): ?>
            <p class="fw-semibold mb-2">Video saat ini</p>
            <div class="media-grid mb-3">
              <?php foreach ($media['videos'] as $m): ?>
                <div class="media-item">
                  <video src="<?= e(upload_url($m['file_path'])) ?>" preload="metadata" muted controls></video>
                  <label class="text-danger"><input type="checkbox" name="delete_media[]" value="<?= (int) $m['id'] ?>"> Hapus video</label>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <div class="form-text mb-3">Centang "Hapus" lalu tekan Simpan untuk menghapus. Foto pertama otomatis menjadi sampul di daftar koleksi.</div>
          <hr>
        <?php endif; ?>

        <div class="upload-zone mb-3">
          <label class="form-label" for="photos"><?= icon('image', 16) ?> <?= $isEdit ? 'Tambah Foto' : 'Unggah Foto' ?></label>
          <input type="file" class="form-control" id="photos" name="photos[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple data-preview="photoPreview" data-max-bytes="<?= (int) $maxBytes ?>">
          <div class="form-text">Boleh pilih beberapa foto sekaligus. Format JPG, PNG, WEBP, GIF. Foto besar otomatis dikecilkan.</div>
          <div class="preview-grid" id="photoPreview"></div>
        </div>

        <div class="upload-zone mb-3">
          <label class="form-label" for="videos"><?= icon('play', 16) ?> <?= $isEdit ? 'Tambah Video' : 'Unggah Video' ?> (opsional)</label>
          <input type="file" class="form-control" id="videos" name="videos[]" accept="video/mp4,video/webm,video/ogg,video/quicktime,.mp4,.webm,.ogv,.mov" multiple data-preview="videoPreview" data-max-bytes="<?= (int) $maxBytes ?>">
          <div class="form-text">Format MP4 (disarankan), WEBM, OGV, MOV. Batas upload server saat ini: <strong><?= e(upload_limit_text()) ?></strong> per pengiriman. Untuk video yang lebih besar, pakai tautan YouTube di bawah.</div>
          <div class="preview-grid" id="videoPreview"></div>
        </div>

        <div>
          <label class="form-label" for="video_url">Atau tautan video YouTube (opsional)</label>
          <input class="form-control" id="video_url" name="video_url" maxlength="255" value="<?= e($val('video_url')) ?>" placeholder="https://www.youtube.com/watch?v=xxxxxxxxxxx">
          <div class="form-text">Cara paling mudah untuk video panjang/berukuran besar: unggah ke YouTube (boleh "Tidak Publik"), lalu tempel tautannya di sini.</div>
        </div>
      </div></div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h2 class="section-title">Status &amp; Tampilan</h2>
        <div class="mb-3">
          <label class="form-label" for="status">Status Ketersediaan</label>
          <select class="form-select" id="status" name="status">
            <?php foreach (Product::STATUSES as $k => $label): ?><option value="<?= e($k) ?>" <?= selected($val('status', 'tersedia'), $k) ?>><?= e($label) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" <?= checked($flag('is_active', true)) ?>>
          <label class="form-check-label" for="is_active">Tampilkan di website</label>
        </div>
        <div class="form-text mb-3">Matikan untuk menyembunyikan produk tanpa menghapusnya.</div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" <?= checked($flag('is_featured', false)) ?>>
          <label class="form-check-label" for="is_featured">Koleksi Pilihan (tampil di Beranda)</label>
        </div>
      </div></div>

      <div class="help-box mb-4">
        <strong>Tips foto yang bagus</strong><br>
        Gunakan cahaya terang, latar polos, dan foto dari beberapa sisi: bilah, pamor, ganja, hulu, dan warangka.
      </div>

      <?php if ($isEdit): ?>
        <div class="card"><div class="card-body small text-muted">
          Dibuat: <?= e(tgl_indo($product['created_at'], true)) ?><br>
          Diubah: <?= e(tgl_indo($product['updated_at'], true)) ?><br>
          Dilihat: <?= (int) $product['views'] ?> kali
        </div></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="sticky-actions">
    <button class="btn btn-gold px-4" type="submit"><?= icon('check', 16) ?> Simpan Produk</button>
    <a class="btn btn-soft" href="<?= e(url('admin/produk')) ?>">Batal</a>
  </div>
</form>
