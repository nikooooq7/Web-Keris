<?php
/**
 * SETTINGS MANAGER
 * ---------------------------------------------------------------
 * Mengelola formulir pengaturan di Panel Admin (Profil Toko, Beranda,
 * Kontak, Tampilan). Daftar isian-nya didefinisikan di app/settings_fields.php
 * — jadi untuk menambah isian baru cukup menambah satu baris di file itu.
 */
class SettingsManager
{
    public static function group(string $key): ?array
    {
        $schema = Setting::schema();
        return $schema[$key] ?? null;
    }

    /** Semua isian dalam satu grup sebagai [kunci => definisi] */
    public static function fields(array $group): array
    {
        $out = [];
        foreach ($group['sections'] as $fields) {
            foreach ($fields as $key => $def) {
                $out[$key] = $def;
            }
        }
        return $out;
    }

    /** Simpan isian dari form. Mengembalikan daftar pesan error (kosong = sukses). */
    public static function save(array $group, array $post, array $files): array
    {
        $errors = [];
        $toSave = [];

        foreach (self::fields($group) as $key => $f) {
            $type = $f['type'] ?? 'text';
            $label = $f['label'] ?? $key;

            if ($type === 'image') {
                $stored = Setting::stored($key);
                if (!empty($post['remove_' . $key])) {
                    if ($stored) {
                        Upload::delete($stored);
                    }
                    Setting::forget($key);
                    continue;
                }
                if (isset($files[$key]) && ($files[$key]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    try {
                        $path = Upload::image($files[$key], 'settings', (int) ($f['max_dim'] ?? 1920));
                        if ($path) {
                            if ($stored) {
                                Upload::delete($stored);
                            }
                            $toSave[$key] = $path;
                        }
                    } catch (Throwable $e) {
                        $errors[] = $label . ': ' . $e->getMessage();
                    }
                }
                continue;
            }

            if (!array_key_exists($key, $post)) {
                continue;
            }
            $value = trim((string) $post[$key]);
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value);
            $max = (int) ($f['max'] ?? ($type === 'textarea' ? 5000 : 255));
            $len = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);

            if (!empty($f['required']) && $value === '') {
                $errors[] = $label . ' wajib diisi.';
                continue;
            }
            if ($len > $max) {
                $errors[] = $label . ' terlalu panjang (maksimal ' . $max . ' karakter).';
                continue;
            }
            if ($value !== '') {
                if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = $label . ' bukan alamat email yang valid.';
                    continue;
                }
                if ($type === 'url' && !preg_match('~^https?://~i', $value)) {
                    $errors[] = $label . ' harus diawali http:// atau https://';
                    continue;
                }
                if ($type === 'number') {
                    if (!ctype_digit($value)) {
                        $errors[] = $label . ' harus berupa angka.';
                        continue;
                    }
                    $n = (int) $value;
                    if (isset($f['min']) && $n < $f['min']) {
                        $errors[] = $label . ' minimal ' . $f['min'] . '.';
                        continue;
                    }
                    if (isset($f['max_value']) && $n > $f['max_value']) {
                        $errors[] = $label . ' maksimal ' . $f['max_value'] . '.';
                        continue;
                    }
                }
                if ($type === 'tel' && !preg_match('/^[0-9+\-\s()]{5,30}$/', $value)) {
                    $errors[] = $label . ' hanya boleh berisi angka, spasi, +, -, dan tanda kurung.';
                    continue;
                }
            }
            $toSave[$key] = $value;
        }

        // Jika ada error pada isian teks, jangan simpan apa pun (agar konsisten)
        if ($errors) {
            // Foto yang sudah terlanjur terunggah tetap tersimpan; hanya teks yang ditahan
            foreach ($toSave as $k => $v) {
                $def = self::fields($group)[$k] ?? [];
                if (($def['type'] ?? 'text') === 'image') {
                    Setting::set($k, $v);
                }
            }
            return $errors;
        }
        Setting::setMany($toSave);
        return [];
    }
}
