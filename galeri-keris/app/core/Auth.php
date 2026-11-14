<?php
/**
 * AUTH — login & logout admin.
 */
class Auth
{
    const MAX_FAILS = 5;      // maksimal salah password berturut-turut
    const LOCK_SECONDS = 300; // lalu dikunci selama 5 menit

    private static $user = null;
    private static $userLoaded = false;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        session_name('keris_sid');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => is_https(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        @session_start();
        Flash::boot();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    /** Data admin yang sedang login (atau null) */
    public static function user(): ?array
    {
        if (self::$userLoaded) {
            return self::$user;
        }
        self::$userLoaded = true;
        if (!empty($_SESSION['uid']) && app_installed()) {
            try {
                self::$user = User::find((int) $_SESSION['uid']);
            } catch (Throwable $e) {
                self::$user = null;
            }
            if (self::$user === null) {
                unset($_SESSION['uid']);
            }
        }
        return self::$user;
    }

    public static function lockedSeconds(): int
    {
        $until = (int) ($_SESSION['login_lock'] ?? 0);
        return $until > time() ? $until - time() : 0;
    }

    public static function attempt(string $username, string $password): bool
    {
        $user = User::findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = (int) $user['id'];
            unset($_SESSION['login_fail'], $_SESSION['login_lock']);
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                User::setPassword((int) $user['id'], $password);
            }
            self::$userLoaded = false;
            return true;
        }
        usleep(400000); // memperlambat tebak-tebakan password
        $_SESSION['login_fail'] = (int) ($_SESSION['login_fail'] ?? 0) + 1;
        if ($_SESSION['login_fail'] >= self::MAX_FAILS) {
            $_SESSION['login_lock'] = time() + self::LOCK_SECONDS;
            $_SESSION['login_fail'] = 0;
        }
        return false;
    }

    public static function logout(): void
    {
        unset($_SESSION['uid']);
        session_regenerate_id(true);
        self::$user = null;
        self::$userLoaded = false;
    }

    /** Wajib login: jika belum, arahkan ke halaman login */
    public static function guard(): void
    {
        if (!self::check()) {
            Flash::add('warning', 'Silakan masuk terlebih dahulu.');
            redirect('admin/login');
        }
    }
}
