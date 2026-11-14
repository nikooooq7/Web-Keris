<?php
/**
 * USER — akun admin.
 */
class User
{
    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByUsername(string $username): ?array
    {
        return Database::one('SELECT * FROM users WHERE username = ?', [$username]);
    }

    public static function create(string $username, string $password, string $displayName): int
    {
        return Database::insert('users', [
            'username'      => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'display_name'  => $displayName,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public static function setPassword(int $id, string $password): void
    {
        Database::update('users', $id, ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public static function updateProfile(int $id, string $username, string $displayName): void
    {
        Database::update('users', $id, ['username' => $username, 'display_name' => $displayName]);
    }
}
