<?php
/**
 * MESSAGE — pesan yang dikirim pengunjung lewat formulir Kontak.
 */
class Message
{
    public static function create(array $data): int
    {
        $data['is_read'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('messages', $data);
    }

    public static function paginate(int $page, int $perPage): array
    {
        $total = (int) Database::value('SELECT COUNT(*) FROM messages');
        $pages = max(1, (int) ceil($total / max(1, $perPage)));
        $page = min(max(1, $page), $pages);
        $items = Database::all(
            'SELECT * FROM messages ORDER BY id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) (($page - 1) * $perPage)
        );
        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public static function latest(int $limit = 5): array
    {
        return Database::all('SELECT * FROM messages ORDER BY id DESC LIMIT ' . (int) $limit);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM messages WHERE id = ?', [$id]);
    }

    public static function markRead(int $id): void
    {
        Database::run('UPDATE messages SET is_read = 1 WHERE id = ?', [$id]);
    }

    public static function unreadCount(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM messages WHERE is_read = 0');
    }

    public static function delete(int $id): void
    {
        Database::delete('messages', $id);
    }
}
