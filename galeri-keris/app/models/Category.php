<?php
/**
 * CATEGORY — kategori produk (Keris, Tombak, Keramik Antik, dll.)
 */
class Category
{
    public static function all(): array
    {
        return Database::all('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
    }

    /** Semua kategori + jumlah produk aktif di dalamnya */
    public static function withCounts(): array
    {
        return Database::all(
            'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) AS product_count
             FROM categories c ORDER BY c.sort_order ASC, c.name ASC'
        );
    }

    /** Untuk halaman admin: jumlah semua produk (termasuk yang disembunyikan) */
    public static function withTotalCounts(): array
    {
        return Database::all(
            'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
             FROM categories c ORDER BY c.sort_order ASC, c.name ASC'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::one('SELECT * FROM categories WHERE slug = ?', [$slug]);
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = slugify($name);
        $slug = $base;
        $i = 2;
        while (true) {
            $row = Database::one('SELECT id FROM categories WHERE slug = ?', [$slug]);
            if (!$row || ($ignoreId !== null && (int) $row['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }

    public static function create(string $name, string $description, int $sort): int
    {
        return Database::insert('categories', [
            'name'        => $name,
            'slug'        => self::uniqueSlug($name),
            'description' => $description,
            'sort_order'  => $sort,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public static function update(int $id, string $name, string $description, int $sort): void
    {
        Database::update('categories', $id, [
            'name'        => $name,
            'slug'        => self::uniqueSlug($name, $id),
            'description' => $description,
            'sort_order'  => $sort,
        ]);
    }

    /** Hapus kategori. Produk di dalamnya tidak ikut terhapus (menjadi "tanpa kategori"). */
    public static function delete(int $id): void
    {
        Database::run('UPDATE products SET category_id = NULL WHERE category_id = ?', [$id]);
        Database::delete('categories', $id);
    }
}
