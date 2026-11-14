<?php
/**
 * FLASH — pesan notifikasi sekali tampil ("Produk berhasil disimpan")
 * dan penyimpanan isian form sementara jika validasi gagal.
 */
class Flash
{
    private static $old = [];

    /** Dipanggil saat sesi dimulai */
    public static function boot(): void
    {
        self::$old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_old']);
    }

    /** $type: success | danger | warning | info */
    public static function add(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /** Ambil semua pesan lalu hapus */
    public static function all(): array
    {
        $msgs = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $msgs;
    }

    public static function keepOld(array $input): void
    {
        unset($input['_token'], $input['password'], $input['current_password'], $input['new_password'],
            $input['new_password_confirm'], $input['admin_password'], $input['admin_password_confirm'], $input['db_pass']);
        $_SESSION['_old'] = $input;
    }

    public static function old(string $key, $default = '')
    {
        return array_key_exists($key, self::$old) ? self::$old[$key] : $default;
    }

    public static function hasOld(): bool
    {
        return !empty(self::$old);
    }
}
