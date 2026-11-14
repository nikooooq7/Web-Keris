<div class="row"><div class="col-lg-6">
  <div class="card"><div class="card-body">
    <form method="post" action="<?= e(url('admin/akun')) ?>" autocomplete="off">
      <?= csrf_field() ?>
      <div class="mb-3"><label class="form-label" for="display_name">Nama Tampilan</label><input class="form-control" id="display_name" name="display_name" maxlength="100" value="<?= e(old('display_name', $user['display_name'])) ?>"></div>
      <div class="mb-3"><label class="form-label" for="username">Username</label><input class="form-control" id="username" name="username" maxlength="40" required value="<?= e(old('username', $user['username'])) ?>"></div>
      <hr>
      <p class="text-muted small">Isi bagian password baru hanya jika Anda ingin menggantinya.</p>
      <div class="mb-3"><label class="form-label" for="new_password">Password Baru (min. 8 karakter)</label><input type="password" class="form-control" id="new_password" name="new_password" minlength="8" autocomplete="new-password"></div>
      <div class="mb-3"><label class="form-label" for="new_password_confirm">Ulangi Password Baru</label><input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" autocomplete="new-password"></div>
      <hr>
      <div class="mb-4"><label class="form-label" for="current_password">Password Saat Ini * (untuk konfirmasi)</label><input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password"></div>
      <button class="btn btn-gold" type="submit">Simpan</button>
    </form>
  </div></div>
</div></div>
