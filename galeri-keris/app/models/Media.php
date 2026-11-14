<?php
/**
 * MEDIA — foto & video milik sebuah produk (tabel product_media).
 */
class Media
{
    public static function forProduct(int $productId): array
    {
        return Database::all(
            'SELECT * FROM product_media WHERE product_id = ? ORDER BY sort_order ASC, id ASC',
            [$productId]
        );
    }

    /** ['images' => [...], 'videos' => [...]] */
    public static function grouped(int $productId): array
    {
        $out = ['images' => [], 'videos' => []];
        foreach (self::forProduct($productId) as $m) {
            $out[$m['type'] === 'video' ? 'videos' : 'images'][] = $m;
        }
        return $out;
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM product_media WHERE id = ?', [$id]);
    }

    public static function add(int $productId, string $type, string $path): int
    {
        $next = (int) Database::value(
            'SELECT COALESCE(MAX(sort_order), -1) + 1 FROM product_media WHERE product_id = ? AND type = ?',
            [$productId, $type]
        );
        return Database::insert('product_media', [
            'product_id' => $productId,
            'type'       => $type,
            'file_path'  => $path,
            'sort_order' => $next,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /** Hapus satu media (file + baris database). Hanya jika memang milik produk tersebut. */
    public static function deleteForProduct(int $productId, int $mediaId): void
    {
        $m = self::find($mediaId);
        if ($m && (int) $m['product_id'] === $productId) {
            Upload::delete($m['file_path']);
            Database::delete('product_media', $mediaId);
        }
    }

    /** Jadikan satu foto sebagai sampul (urutan pertama) */
    public static function setCover(int $productId, int $mediaId): void
    {
        $images = Database::all(
            "SELECT id FROM product_media WHERE product_id = ? AND type = 'image' ORDER BY sort_order ASC, id ASC",
            [$productId]
        );
        $ids = array_map(function ($r) {
            return (int) $r['id'];
        }, $images);
        if (!in_array($mediaId, $ids, true)) {
            return;
        }
        $ordered = array_merge([$mediaId], array_values(array_diff($ids, [$mediaId])));
        foreach ($ordered as $i => $id) {
            Database::run('UPDATE product_media SET sort_order = ? WHERE id = ?', [$i, $id]);
        }
    }
}
