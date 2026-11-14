<?php
$blocked = false;
foreach ($checks as $c) {
    if ($c[2] && !$c[1]) {
        $blocked = true;
    }
}
?>
<div class="panel mb-4">
  <h2 class="h5 mb-3">1. Pemeriksaan Server</h2>
  <ul class="list-unstyled mb-0">
    <?php foreach ($checks as $c): ?>
      <li class="d-flex gap-2 align-items-start py-1">
        <?php if ($c[1]): ?><span class="text-success"><?= icon('check', 18) ?></span>
        <?php elseif ($c[2]): ?><span class="text-danger fw-bold">&#10007;</span>
        <?php else: ?><span class="text-warning fw-bold">!</span><?php endif; ?>
        <span><?= e($c[0]) ?><?php if (!$c[1] && !$c[2]): ?> <small class="text-muted">(disarankan, tidak wajib)</small><?php endif; ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php if ($blocked): ?>
    <div class="alert alert-danger mt-3 mb-0">Ada syarat wajib yang belum terpenuhi (tanda &#10007;). Perbaiki dulu — lihat bagian "Masalah Umum" di PANDUAN.md.</div>
  <?php endif; ?>
</div>

<form method="post" action="<?= e(url('install')) ?>" autocomplete="off">
  <?= csrf_field() ?>

  <div class="panel mb-4">
    <h2 class="h5 mb-1">2. Database MySQL</h2>
    <p class="text-muted small">Buat dulu database kosong di hosting/XAMPP Anda (lihat PANDUAN.md), lalu isi datanya di sini.</p>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label" for="db_host">Host</label><input class="form-control" id="db_host" name="db_host" value="<?= e(old('db_host', 'localhost')) ?>" required></div>
      <div class="col-md-6"><label class="form-label" for="db_name">Nama Database *</label><input class="form-control" id="db_name" name="db_name" value="<?= e(old('db_name')) ?>" required></div>
      <div class="col-md-6"><label class="form-label" for="db_user">User Database *</label><input class="form-control" id="db_user" name="db_user" value="<?= e(old('db_user')) ?>" required></div>
      <div class="col-md-6"><label class="form-label" for="db_pass">Password Database</label><input type="password" class="form-control" id="db_pass" name="db_pass" autocomplete="new-password"></div>
    </div>
  </div>

  <div class="panel mb-4">
    <h2 class="h5 mb-3">3. Toko &amp; Akun Admin</h2>
    <div class="row g-3">
      <div class="col-12"><label class="form-label" for="shop_name">Nama Toko / Galeri *</label><input class="form-control" id="shop_name" name="shop_name" maxlength="80" value="<?= e(old('shop_name', 'Galeri Keris Nusantara')) ?>" required></div>
      <div class="col-12"><label class="form-label" for="admin_username">Username Admin *</label><input class="form-control" id="admin_username" name="admin_username" maxlength="40" value="<?= e(old('admin_username', 'admin')) ?>" required></div>
      <div class="col-md-6"><label class="form-label" for="admin_password">Password Admin * (min. 8 karakter)</label><input type="password" class="form-control" id="admin_password" name="admin_password" minlength="8" autocomplete="new-password" required></div>
      <div class="col-md-6"><label class="form-label" for="admin_password_confirm">Ulangi Password *</label><input type="password" class="form-control" id="admin_password_confirm" name="admin_password_confirm" minlength="8" autocomplete="new-password" required></div>
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="sample" name="sample" value="1" checked>
          <label class="form-check-label" for="sample">Isi dengan contoh kategori &amp; produk (agar tampilan langsung terlihat; bisa dihapus nanti)</label>
        </div>
      </div>
    </div>
  </div>

  <div class="d-grid"><button type="submit" class="btn btn-gold btn-lg" <?= $blocked ? 'disabled' : '' ?>>Pasang Website Sekarang</button></div>
</form>
