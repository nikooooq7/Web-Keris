<?php
$shopName = setting('shop_name', 'Galeri Keris');
$logo = media_url(setting('site_logo')) ?: asset('img/logo.svg');
$locked = Auth::lockedSeconds();
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Masuk Admin — <?= e($shopName) ?></title>
  <link rel="icon" href="<?= e($logo) ?>">
  <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body>
<div class="login-wrap">
  <div class="login-card mt-5">
    <img class="logo" src="<?= e($logo) ?>" alt="">
    <h1 class="h4 text-center mb-1"><?= e($shopName) ?></h1>
    <p class="text-center text-muted mb-4">Masuk ke Panel Admin</p>

    <?php foreach (Flash::all() as $f): ?>
      <div class="alert alert-<?= e($f['type']) ?> py-2"><?= e($f['message']) ?></div>
    <?php endforeach; ?>

    <form method="post" action="<?= e(url('admin/login')) ?>">
      <?= csrf_field() ?>
      <div class="mb-3"><label class="form-label" for="username">Username</label>
        <input class="form-control" id="username" name="username" value="<?= e(old('username')) ?>" autocomplete="username" required autofocus <?= $locked ? 'disabled' : '' ?>></div>
      <div class="mb-4"><label class="form-label" for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required <?= $locked ? 'disabled' : '' ?>></div>
      <button class="btn btn-gold w-100 py-2" type="submit" <?= $locked ? 'disabled' : '' ?>>Masuk</button>
    </form>
    <div class="text-center mt-3"><a class="small" href="<?= e(url('')) ?>">&larr; Kembali ke website</a></div>
  </div>
</div>
</body>
</html>
