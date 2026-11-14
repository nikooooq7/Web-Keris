<?php
/**
 * Form pengaturan umum — dibuat OTOMATIS dari app/settings_fields.php.
 * Variabel: $group (grup pengaturan), $formPath (alamat form)
 */
$hasOld = Flash::hasOld();
?>
<?php if (!empty($group['intro'])): ?><div class="help-box mb-4"><?= e($group['intro']) ?></div><?php endif; ?>

<form method="post" action="<?= e(url($formPath)) ?>" enctype="multipart/form-data" novalidate>
  <?= csrf_field() ?>
  <?php foreach ($group['sections'] as $sectionTitle => $fields): ?>
    <div class="card mb-4"><div class="card-body">
      <h2 class="section-title"><?= e($sectionTitle) ?></h2>
      <div class="row g-3">
        <?php foreach ($fields as $key => $f):
            $type = $f['type'] ?? 'text';
            $label = $f['label'] ?? $key;
            $value = $hasOld ? old($key, setting($key)) : setting($key);
            $id = 'f_' . $key;
        ?>
          <div class="<?= in_array($type, ['textarea', 'image'], true) ? 'col-12' : 'col-md-6' ?>">
            <label class="form-label" for="<?= e($id) ?>"><?= e($label) ?><?= !empty($f['required']) ? ' *' : '' ?></label>

            <?php if ($type === 'image'):
                $stored = Setting::stored($key);
                $cur = media_url($value);
            ?>
              <div class="img-setting">
                <div class="current">
                  <?php if ($cur): ?><img src="<?= e($cur) ?>" alt="Gambar saat ini"><?php else: ?><div class="text-muted small">Belum ada gambar</div><?php endif; ?>
                  <div class="small text-muted mt-1"><?= $stored ? 'Gambar buatan Anda' : 'Gambar bawaan' ?></div>
                </div>
                <div class="controls">
                  <input type="file" class="form-control" id="<?= e($id) ?>" name="<?= e($key) ?>" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="prev_<?= e($key) ?>">
                  <div class="preview-grid" id="prev_<?= e($key) ?>"></div>
                  <?php if ($stored): ?>
                    <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="rm_<?= e($key) ?>" name="remove_<?= e($key) ?>" value="1"><label class="form-check-label" for="rm_<?= e($key) ?>">Hapus gambar ini (kembali ke bawaan)</label></div>
                  <?php endif; ?>
                </div>
              </div>

            <?php elseif ($type === 'textarea'): ?>
              <textarea class="form-control" id="<?= e($id) ?>" name="<?= e($key) ?>" rows="<?= (int) ($f['rows'] ?? 4) ?>" maxlength="<?= (int) ($f['max'] ?? 5000) ?>"><?= e($value) ?></textarea>

            <?php else:
                $htmlType = in_array($type, ['email', 'url', 'tel'], true) ? $type : 'text';
            ?>
              <input class="form-control" type="<?= e($htmlType) ?>" id="<?= e($id) ?>" name="<?= e($key) ?>" value="<?= e($value) ?>"
                     maxlength="<?= (int) ($f['max'] ?? 255) ?>" <?= $type === 'number' ? 'inputmode="numeric"' : '' ?> <?= !empty($f['required']) ? 'required' : '' ?>>
            <?php endif; ?>

            <?php if (!empty($f['help'])): ?><div class="form-text"><?= e($f['help']) ?></div><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div></div>
  <?php endforeach; ?>

  <div class="sticky-actions">
    <button class="btn btn-gold px-4" type="submit"><?= icon('check', 16) ?> Simpan Perubahan</button>
    <a class="btn btn-soft" href="<?= e(url('')) ?>" target="_blank" rel="noopener"><?= icon('external', 15) ?> Lihat Hasilnya di Website</a>
  </div>
</form>
