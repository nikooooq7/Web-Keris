<?php
/**
 * SETTING — pengaturan website (nama toko, teks beranda, foto profil, dll.)
 * Disimpan di tabel `settings`. Jika belum pernah diubah di Panel Admin,
 * dipakai nilai bawaan dari app/settings_fields.php
 */
class Setting
{
    private static $cache = null;
    private static $schema = null;
    private static $defaults = null;

    /** Definisi semua isian pengaturan */
    public static function schema(): array
    {
        if (self::$schema === null) {
            self::$schema = require APP_PATH . '/settings_fields.php';
        }
        return self::$schema;
    }

    public static function defaults(): array
    {
        if (self::$defaults === null) {
            self::$defaults = [];
            foreach (self::schema() as $group) {
                foreach ($group['sections'] as $fields) {
                    foreach ($fields as $key => $def) {
                        self::$defaults[$key] = $def['default'] ?? '';
                    }
                }
            }
        }
        return self::$defaults;
    }

    private static function load(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                foreach (Database::all('SELECT setting_key, setting_value FROM settings') as $row) {
                    self::$cache[$row['setting_key']] = $row['setting_value'];
                }
            } catch (Throwable $e) {
                // Database belum siap: pakai nilai bawaan
            }
        }
        return self::$cache;
    }

    /** Nilai yang tersimpan di database (null jika belum pernah diubah) */
    public static function stored(string $key): ?string
    {
        $all = self::load();
        return array_key_exists($key, $all) ? $all[$key] : null;
    }

    public static function get(string $key, $default = null)
    {
        $all = self::load();
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }
        $defaults = self::defaults();
        if (array_key_exists($key, $defaults)) {
            return $defaults[$key];
        }
        return $default;
    }

    public static function set(string $key, string $value): void
    {
        $exists = Database::value('SELECT COUNT(*) FROM settings WHERE setting_key = ?', [$key]);
        if ((int) $exists > 0) {
            Database::run('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
        } else {
            Database::run('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
        }
        self::$cache = null;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::set($key, (string) $value);
        }
    }

    /** Hapus isian sehingga kembali ke nilai bawaan */
    public static function forget(string $key): void
    {
        Database::run('DELETE FROM settings WHERE setting_key = ?', [$key]);
        self::$cache = null;
    }
}
