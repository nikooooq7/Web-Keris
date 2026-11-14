<?php $doneCount = count(array_filter($checklist, function ($c) { return $c[1]; })); ?>
<div class="row g-3 mb-4">
  <?php
  $cards = [
      ['Total Produk', $stats['total'], 'box'],
      ['Tersedia', $stats['available'], 'check'],
      ['Terjual', $stats['sold'], 'tag'],
      ['Pesan Belum Dibaca', $unread, 'chat'],
  ];
  foreach ($cards as $c): ?>
    <div class="col-6 col-xl-3">
      <div class="card"><div class="card-body stat">
        <div class="stat-icon"><?= icon($c[2], 26) ?></div>
        <div><div class="stat-num"><?= (int) $c[1] ?></div><small><?= e($c[0]) ?></small></div>
      </div></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between"><span>Langkah Awal</span><span class="text-muted small"><?= $doneCount ?>/<?= count($checklist) ?> selesai</span></div>
      <div class="card-body">
        <ul class="list-unstyled checklist mb-0">
          <?php foreach ($checklist as $c): ?>
            <li>
              <?php if ($c[1]): ?><span class="done"><?= icon('check', 20) ?></span><span class="text-muted text-decoration-line-through"><?= e($c[0]) ?></span>
              <?php else: ?><span class="todo">&#9675;</span><a href="<?= e(url($c[2])) ?>"><?= e($c[0]) ?></a><?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="help-box mt-3">Semua tulisan, foto, dan data website ini bisa diubah dari menu di sebelah kiri — tanpa menyentuh kode.</div>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center"><span>Produk Terbaru</span><a class="btn btn-sm btn-gold" href="<?= e(url('admin/produk/tambah')) ?>"><?= icon('plus', 14) ?> Tambah</a></div>
      <div class="table-responsive">
        <table class="table table-hover">
          <tbody>
          <?php foreach ($latest as $p): list($stLabel, $stClass) = product_status($p['status']); ?>
            <tr>
              <td style="width:80px"><img class="thumb-sm" src="<?= e(!empty($p['cover']) ? upload_url($p['cover']) : asset('img/placeholder-product.svg')) ?>" alt=""></td>
              <td><a href="<?= e(url('admin/produk/' . $p['id'] . '/edit')) ?>" class="fw-semibold"><?= e(str_limit($p['name'], 50)) ?></a><br><small class="text-muted"><?= e(rupiah($p['price'])) ?></small></td>
              <td class="text-end"><span class="badge-status <?= e($stClass) ?>"><?= e($stLabel) ?></span></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$latest): ?><tr><td class="text-center text-muted py-4">Belum ada produk.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex justify-content-between"><span>Pesan Terbaru</span><a href="<?= e(url('admin/pesan')) ?>" class="small">Lihat semua</a></div>
      <div class="list-group list-group-flush">
        <?php foreach ($messages as $m): ?>
          <a class="list-group-item list-group-item-action" href="<?= e(url('admin/pesan/' . $m['id'])) ?>">
            <div class="d-flex justify-content-between"><strong><?= e($m['name']) ?> <?= !$m['is_read'] ? '<span class="badge text-bg-danger">Baru</span>' : '' ?></strong><small class="text-muted"><?= e(tgl_indo($m['created_at'], true)) ?></small></div>
            <small class="text-muted"><?= e(str_limit($m['body'], 90)) ?></small>
          </a>
        <?php endforeach; ?>
        <?php if (!$messages): ?><div class="list-group-item text-center text-muted py-4">Belum ada pesan.</div><?php endif; ?>
      </div>
    </div>
  </div>
</div>
