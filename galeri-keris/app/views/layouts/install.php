<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>Instalasi — Galeri Keris</title>
  <link rel="icon" href="<?= e(asset('img/logo.svg')) ?>">
  <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body style="background-image:url('<?= e(asset('img/bg-hero.svg')) ?>');background-size:cover;background-attachment:fixed">
<main class="py-5">
  <div class="container" style="max-width:760px">
    <div class="text-center mb-4">
      <img src="<?= e(asset('img/logo.svg')) ?>" alt="" width="96" height="96">
      <h1 class="h3 mt-3">Instalasi Website</h1>
      <p class="text-muted mb-0">Cukup isi formulir di bawah ini sekali saja. Prosesnya hanya sekitar 1 menit.</p>
    </div>
    <?php foreach (Flash::all() as $f): ?>
      <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </div>
</main>
</body>
</html>
