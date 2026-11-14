<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead><tr><th>Pengirim</th><th>Subjek / Pesan</th><th>Tanggal</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($result['items'] as $m): ?>
        <tr class="<?= !$m['is_read'] ? 'msg-unread' : '' ?>">
          <td><?= e($m['name']) ?><br><small class="text-muted fw-normal"><?= e($m['email'] !== '' ? $m['email'] : $m['phone']) ?></small></td>
          <td><a href="<?= e(url('admin/pesan/' . $m['id'])) ?>"><?= e($m['subject'] !== '' ? $m['subject'] : '(tanpa subjek)') ?></a><br><small class="text-muted fw-normal"><?= e(str_limit($m['body'], 80)) ?></small></td>
          <td class="text-nowrap"><small><?= e(tgl_indo($m['created_at'], true)) ?></small></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-outline-gold" href="<?= e(url('admin/pesan/' . $m['id'])) ?>">Buka</a>
            <form method="post" action="<?= e(url('admin/pesan/' . $m['id'] . '/hapus')) ?>" class="d-inline" data-confirm="Hapus pesan ini?"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit"><?= icon('trash', 15) ?></button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$result['items']): ?><tr><td colspan="4" class="text-center text-muted py-5">Belum ada pesan masuk.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3"><?= pagination_html($result['page'], $result['pages'], 'admin/pesan') ?></div>
