<div class="row"><div class="col-lg-8">
  <div class="card">
    <div class="card-header"><?= e($msg['subject'] !== '' ? $msg['subject'] : '(tanpa subjek)') ?></div>
    <div class="card-body">
      <dl class="row mb-4">
        <dt class="col-sm-3">Nama</dt><dd class="col-sm-9"><?= e($msg['name']) ?></dd>
        <?php if ($msg['email'] !== ''): ?><dt class="col-sm-3">Email</dt><dd class="col-sm-9"><a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a></dd><?php endif; ?>
        <?php if ($msg['phone'] !== ''): ?><dt class="col-sm-3">Telepon</dt><dd class="col-sm-9"><?= e($msg['phone']) ?>
          <?php if (wa_number($msg['phone'])): ?> &middot; <a target="_blank" rel="noopener" href="<?= e(wa_link($msg['phone'])) ?>">Balas via WhatsApp</a><?php endif; ?></dd><?php endif; ?>
        <dt class="col-sm-3">Diterima</dt><dd class="col-sm-9"><?= e(tgl_indo($msg['created_at'], true)) ?></dd>
      </dl>
      <div class="p-3 rounded" style="background:#fbf8ef;border:1px solid #e3d9bd"><?= nl2br(e($msg['body'])) ?></div>
    </div>
  </div>
  <div class="d-flex gap-2 mt-3">
    <a class="btn btn-soft" href="<?= e(url('admin/pesan')) ?>">&larr; Kembali</a>
    <form method="post" action="<?= e(url('admin/pesan/' . $msg['id'] . '/hapus')) ?>" data-confirm="Hapus pesan ini?"><?= csrf_field() ?><button class="btn btn-outline-danger" type="submit"><?= icon('trash', 15) ?> Hapus</button></form>
  </div>
</div></div>
