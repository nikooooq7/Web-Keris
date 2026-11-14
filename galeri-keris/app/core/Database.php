<?php
/**
 * DATABASE
 * ---------------------------------------------------------------
 * Pembungkus sederhana untuk koneksi MySQL (PDO).
 * Semua query memakai "prepared statement" sehingga aman dari SQL injection.
 */
class Database
{
    private static $pdo = null;

    /** Koneksi utama (dibuat sekali, memakai data di config/config.php) */
    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $cfg = app_config('db');
            if (!$cfg) {
                throw new RuntimeException('Konfigurasi database belum tersedia.');
            }
            self::$pdo = self::connect($cfg);
        }
        return self::$pdo;
    }

    /** Membuat koneksi baru dari array konfigurasi (dipakai juga oleh installer) */
    public static function connect(array $cfg): PDO
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        if (($cfg['driver'] ?? 'mysql') === 'sqlite') {
            $pdo = new PDO('sqlite:' . $cfg['path'], null, null, $options);
            $pdo->exec('PRAGMA foreign_keys = ON');
            return $pdo;
        }
        $host = $cfg['host'] ?? 'localhost';
        $port = (int) ($cfg['port'] ?? 3306);
        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $cfg['name'] . ';charset=utf8mb4';
        $options[PDO::ATTR_EMULATE_PREPARES] = false;
        return new PDO($dsn, $cfg['user'] ?? '', $cfg['pass'] ?? '', $options);
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute(array_values($params));
        return $stmt;
    }

    /** Ambil banyak baris */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /** Ambil satu baris (atau null) */
    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Ambil satu nilai (mis. COUNT) */
    public static function value(string $sql, array $params = [])
    {
        $v = self::run($sql, $params)->fetchColumn();
        return $v === false ? null : $v;
    }

    /** INSERT dari array [kolom => nilai], mengembalikan ID baru */
    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $sql = 'INSERT INTO `' . $table . '` (`' . implode('`, `', $cols) . '`) VALUES (' . implode(', ', array_fill(0, count($cols), '?')) . ')';
        self::run($sql, array_values($data));
        return (int) self::pdo()->lastInsertId();
    }

    /** UPDATE dari array [kolom => nilai] untuk satu baris berdasarkan id */
    public static function update(string $table, int $id, array $data): void
    {
        $set = [];
        foreach (array_keys($data) as $col) {
            $set[] = '`' . $col . '` = ?';
        }
        $params = array_values($data);
        $params[] = $id;
        self::run('UPDATE `' . $table . '` SET ' . implode(', ', $set) . ' WHERE id = ?', $params);
    }

    public static function delete(string $table, int $id): void
    {
        self::run('DELETE FROM `' . $table . '` WHERE id = ?', [$id]);
    }

    /** Menjalankan isi file .sql (dipakai installer untuk membuat tabel) */
    public static function runSqlFile(PDO $pdo, string $file): void
    {
        $sql = file_get_contents($file);
        $sql = preg_replace('/^\s*--.*$/m', '', $sql);
        foreach (explode(';', $sql) as $statement) {
            $statement = trim($statement);
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }
    }
}
