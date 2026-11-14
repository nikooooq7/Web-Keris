<?php
/** Halaman error server. Dibuat mandiri (tanpa database) agar tetap tampil walau database bermasalah. */
$home = function_exists('url') ? url('') : '/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Terjadi Kesalahan</title>
  <style>
    body{margin:0;min-height:100vh;display:grid;place-items:center;background:#0c0804;color:#e9dcc0;font-family:Georgia,serif;text-align:center;padding:1.5rem}
    h1{color:#f1d98a;letter-spacing:.05em}
    .code{font-size:5rem;color:#d4af37;line-height:1}
    a{display:inline-block;margin-top:1rem;padding:.7rem 1.6rem;background:#d4af37;color:#1a1205;text-decoration:none;font-weight:bold}
    code{display:block;margin:1rem auto;max-width:640px;padding:.8rem;background:#1e150b;border:1px solid #8a6a1f;color:#f1d98a;text-align:left;word-break:break-word}
  </style>
</head>
<body>
  <div>
    <div class="code">500</div>
    <h1>Terjadi kesalahan pada server</h1>
    <p>Mohon maaf, ada gangguan sementara. Silakan coba lagi beberapa saat lagi.</p>
    <?php if (!empty($debugMessage)): ?><code><?= htmlspecialchars($debugMessage, ENT_QUOTES, 'UTF-8') ?></code><?php endif; ?>
    <a href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>">Kembali ke Beranda</a>
  </div>
</body>
</html>
