<?php
/**
 * PRODUCT — barang koleksi (keris, tombak, barang antik, dll.)
 */
class Product
{
    const STATUSES = [
        'tersedia' => 'Tersedia',
        'dipesan'  => 'Dipesan',
        'terjual'  => 'Terjual',
    ];

    /** Query dasar: data produk + nama kategori + foto sampul */
    private static function baseSelect(): string
    {
        return "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                (SELECT m.file_path FROM product_media m
                  WHERE m.product_id = p.id AND m.type = 'image'
                  ORDER BY m.sort_order ASC, m.id ASC LIMIT 1) AS cover
                FROM products p LEFT JOIN categories c ON c.id = p.category_id";
    }

    /**
     * Daftar produk berhalaman.
     * $filters: q (kata kunci), category (slug), category_id, status, active (1 = hanya yang tampil di website)
     */
    public static function paginate(array $filters, int $page, int $perPage): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['active'])) {
            $where[] = 'p.is_active = 1';
        }
        if (!empty($filters['q'])) {
            $like = '%' . $filters['q'] . '%';
            $where[] = '(p.name LIKE ? OR p.description LIKE ? OR p.origin LIKE ? OR p.material LIKE ? OR p.era LIKE ?)';
            array_push($params, $like, $like, $like, $like, $like);
        }
        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = $filters['category'];
        }
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'p.status = ?';
            $params[] = $filters['status'];
        }
        $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $total = (int) Database::value(
            'SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id' . $w,
            $params
        );
        $perPage = max(1, $perPage);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = Database::all(
            self::baseSelect() . $w . ' ORDER BY p.id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset,
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public static function find(int $id): ?array
    {
        return Database::one(self::baseSelect() . ' WHERE p.id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::one(self::baseSelect() . ' WHERE p.slug = ? AND p.is_active = 1', [$slug]);
    }

    /** Koleksi pilihan untuk beranda; jika belum ada yang ditandai, pakai produk terbaru */
    public static function featured(int $limit = 8): array
    {
        $limit = max(1, $limit);
        $items = Database::all(
            self::baseSelect() . ' WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.id DESC LIMIT ' . $limit
        );
        if (!$items) {
            $items = Database::all(self::baseSelect() . ' WHERE p.is_active = 1 ORDER BY p.id DESC LIMIT ' . $limit);
        }
        return $items;
    }

    /** Produk terkait (kategori yang sama, selain produk ini) */
    public static function related(array $product, int $limit = 4): array
    {
        $limit = max(1, $limit);
        if (!empty($product['category_id'])) {
            return Database::all(
                self::baseSelect() . ' WHERE p.is_active = 1 AND p.category_id = ? AND p.id <> ? ORDER BY p.id DESC LIMIT ' . $limit,
                [(int) $product['category_id'], (int) $product['id']]
            );
        }
        return Database::all(
            self::baseSelect() . ' WHERE p.is_active = 1 AND p.id <> ? ORDER BY p.id DESC LIMIT ' . $limit,
            [(int) $product['id']]
        );
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = slugify($name);
        $slug = $base;
        $i = 2;
        while (true) {
            $row = Database::one('SELECT id FROM products WHERE slug = ?', [$slug]);
            if (!$row || ($ignoreId !== null && (int) $row['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }

    public static function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['slug'] = self::uniqueSlug($data['name']);
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        return Database::insert('products', $data);
    }

    public static function update(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        Database::update('products', $id, $data);
    }

    public static function incrementViews(int $id): void
    {
        Database::run('UPDATE products SET views = views + 1 WHERE id = ?', [$id]);
    }

    /** Hapus produk beserta semua foto & videonya */
    public static function delete(int $id): void
    {
        foreach (Media::forProduct($id) as $m) {
            Upload::delete($m['file_path']);
        }
        Database::run('DELETE FROM product_media WHERE product_id = ?', [$id]);
        Database::delete('products', $id);
    }

    /** Angka ringkasan untuk dashboard admin */
    public static function stats(): array
    {
        return [
            'total'     => (int) Database::value('SELECT COUNT(*) FROM products'),
            'available' => (int) Database::value("SELECT COUNT(*) FROM products WHERE status = 'tersedia'"),
            'sold'      => (int) Database::value("SELECT COUNT(*) FROM products WHERE status = 'terjual'"),
            'hidden'    => (int) Database::value('SELECT COUNT(*) FROM products WHERE is_active = 0'),
        ];
    }
}
