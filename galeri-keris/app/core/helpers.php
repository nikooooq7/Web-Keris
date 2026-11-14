<?php
/**
 * HELPERS — kumpulan fungsi bantu yang dipakai di seluruh website.
 */

/* ------------------------------------------------------------------
 * Konfigurasi & status
 * ------------------------------------------------------------------ */

/** Ambil nilai konfigurasi, contoh: app_config('db.host') */
function app_config(?string $key = null, $default = null)
{
    $cfg = $GLOBALS['APP_CONFIG'] ?? [];
    if ($key === null) {
        return $cfg;
    }
    foreach (explode('.', $key) as $part) {
        if (is_array($cfg) && array_key_exists($part, $cfg)) {
            $cfg = $cfg[$part];
        } else {
            return $default;
        }
    }
    return $cfg;
}

/** Apakah website sudah diinstal? (file config/config.php ada) */
function app_installed(): bool
{
    return is_file(CONFIG_PATH . '/config.php');
}

function log_error($e): void
{
    $dir = STORAGE_PATH . '/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $line = '[' . date('Y-m-d H:i:s') . '] ' . get_class($e) . ': ' . $e->getMessage()
        . ' di ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    @file_put_contents($dir . '/error.log', $line, FILE_APPEND);
}

/* ------------------------------------------------------------------
 * Escape output (WAJIB dipakai saat menampilkan data ke HTML)
 * ------------------------------------------------------------------ */

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* ------------------------------------------------------------------
 * URL & path
 * ------------------------------------------------------------------ */

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443)
        || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

/** Folder dasar website (kosong jika di domain utama, atau mis. "/toko" jika di sub-folder) */
function app_base(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(dirname($script), '/');
    if ($dir === '.' || $dir === '\\') {
        $dir = '';
    }
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($dir !== '' && $uri !== $dir && strpos($uri, $dir . '/') !== 0) {
        // Website dibuka lewat .htaccess di folder induk, jadi "/public" tidak muncul di URL
        $dir = (string) preg_replace('#/public$#', '', $dir);
    }
    return $base = $dir;
}

/** Alamat halaman: url('koleksi') => /koleksi */
function url(string $path = ''): string
{
    return app_base() . '/' . ltrim($path, '/');
}

/** Alamat lengkap (https://domain.com/...) — dipakai untuk tautan WhatsApp/berbagi */
function absolute_url(string $path = ''): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return (is_https() ? 'https' : 'http') . '://' . $host . url($path);
}

/** Alamat file di public/assets (css, js, gambar). Otomatis menambah ?v= agar cache browser ter-update */
function asset(string $path): string
{
    $file = PUBLIC_PATH . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? '?v=' . filemtime($file) : '';
    return url('assets/' . ltrim($path, '/')) . $v;
}

/** Alamat file hasil upload (foto/video) */
function upload_url(string $relative): string
{
    return url('uploads/' . ltrim($relative, '/'));
}

/**
 * Alamat gambar dari pengaturan: bisa gambar bawaan ("assets/img/...") atau hasil upload ("settings/abc.jpg").
 */
function media_url(?string $value): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (strpos($value, 'assets/') === 0) {
        return asset(substr($value, 7));
    }
    return upload_url($value);
}

/** Path halaman saat ini (tanpa folder dasar), contoh "/koleksi/keris-luk-7" */
function request_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $uri = rawurldecode($uri);
    $base = app_base();
    if ($base !== '' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }
    $uri = '/' . trim($uri, '/');
    if ($uri === '/index.php') {
        $uri = '/';
    }
    return $uri;
}

/** Beri class "active" pada menu yang sedang dibuka */
function nav_active(string $prefix): string
{
    $path = request_path();
    if ($prefix === '/') {
        return $path === '/' ? 'active' : '';
    }
    return ($path === $prefix || strpos($path, $prefix . '/') === 0) ? 'active' : '';
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/** Kembali ke halaman sebelumnya (hanya jika dari website ini sendiri) */
function redirect_back(string $fallback = ''): void
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '') {
        $host = parse_url($ref, PHP_URL_HOST);
        $myHost = parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
        if ($host && $myHost && strcasecmp($host, $myHost) === 0) {
            header('Location: ' . $ref);
            exit;
        }
    }
    redirect($fallback);
}

/* ------------------------------------------------------------------
 * Tampilan (view)
 * ------------------------------------------------------------------ */

/**
 * Menampilkan file tampilan dari app/views/, dibungkus layout.
 * Variabel apa pun yang di-set di dalam view (mis. $pageTitle) bisa dibaca oleh layout.
 */
function render_view(string $view, array $data = [], ?string $layout = 'layouts/public'): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    require APP_PATH . '/views/' . $view . '.php';
    $content = ob_get_clean();
    if ($layout === null) {
        echo $content;
        return;
    }
    require APP_PATH . '/views/' . $layout . '.php';
}

/** Ambil nilai pengaturan website, contoh: setting('shop_name') */
function setting(string $key, $default = null)
{
    return Setting::get($key, $default);
}

/* ------------------------------------------------------------------
 * Membaca isian form / alamat dengan aman
 * ------------------------------------------------------------------ */

/** Ambil teks dari form (POST), sudah dirapikan & dibatasi panjangnya */
function post_str(string $key, int $max = 255): string
{
    $v = $_POST[$key] ?? '';
    return clean_str(is_string($v) ? $v : '', $max);
}

/** Ambil teks dari alamat (GET), mis. ?q=keris */
function get_str(string $key, int $max = 255): string
{
    $v = $_GET[$key] ?? '';
    return clean_str(is_string($v) ? $v : '', $max);
}

function clean_str(string $v, int $max): string
{
    $v = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $v));
    if (function_exists('mb_substr')) {
        return mb_substr($v, 0, $max);
    }
    return substr($v, 0, $max);
}

/* ------------------------------------------------------------------
 * Form, CSRF, notifikasi
 * ------------------------------------------------------------------ */

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

/** Nilai isian form sebelumnya (dipakai saat validasi gagal agar isian tidak hilang) */
function old(string $key, $default = '')
{
    return Flash::old($key, $default);
}

function checked($condition): string
{
    return $condition ? 'checked' : '';
}

function selected($a, $b): string
{
    return ((string) $a === (string) $b) ? 'selected' : '';
}

/* ------------------------------------------------------------------
 * Format teks, angka, tanggal
 * ------------------------------------------------------------------ */

function rupiah($number): string
{
    if ($number === null || $number === '') {
        return 'Hubungi Kami';
    }
    return 'Rp ' . number_format((float) $number, 0, ',', '.');
}

/** Ubah isian harga ("Rp 1.500.000,-") menjadi angka 1500000, atau null jika kosong */
function parse_price($value): ?int
{
    $v = trim((string) $value);
    $v = preg_replace('/,(-|00)$/', '', $v);
    $digits = preg_replace('/\D+/', '', $v);
    if ($digits === '') {
        return null;
    }
    return (int) substr($digits, 0, 15);
}

function tgl_indo(?string $datetime, bool $withTime = false): string
{
    if (!$datetime) {
        return '-';
    }
    $ts = strtotime($datetime);
    if (!$ts) {
        return '-';
    }
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $s = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    if ($withTime) {
        $s .= ', ' . date('H:i', $ts);
    }
    return $s;
}

function slugify(string $text): string
{
    $text = trim($text);
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($t !== false && $t !== '') {
            $text = $t;
        }
    }
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? substr($text, 0, 120) : 'item';
}

/** Potong teks menjadi ringkas (tanpa tag HTML) */
function str_limit(?string $text, int $limit = 120): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
    if (function_exists('mb_strlen')) {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $limit)) . '…';
    }
    return strlen($text) <= $limit ? $text : rtrim(substr($text, 0, $limit)) . '...';
}

/** Teks panjang → paragraf HTML yang aman (baris kosong = paragraf baru) */
function format_text(?string $text): string
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }
    $out = '';
    foreach (preg_split('/\R{2,}/', $text) as $para) {
        $out .= '<p>' . nl2br(e(trim($para))) . '</p>';
    }
    return $out;
}

function format_bytes(int $bytes): string
{
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 1) . ' GB';
    }
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024) . ' KB';
    }
    return $bytes . ' B';
}

function ini_to_bytes(string $v): int
{
    $v = trim($v);
    if ($v === '') {
        return 0;
    }
    $last = strtolower(substr($v, -1));
    $n = (int) $v;
    switch ($last) {
        case 'g':
            $n *= 1024;
            // no break
        case 'm':
            $n *= 1024;
            // no break
        case 'k':
            $n *= 1024;
    }
    return $n;
}

/** Batas upload yang berlaku di server (dalam teks, mis. "8 MB") */
function upload_limit_text(): string
{
    $a = ini_to_bytes((string) ini_get('upload_max_filesize'));
    $b = ini_to_bytes((string) ini_get('post_max_size'));
    $limit = ($b > 0 && $a > 0) ? min($a, $b) : max($a, $b);
    return $limit > 0 ? format_bytes($limit) : 'tidak terbatas';
}

/* ------------------------------------------------------------------
 * WhatsApp, YouTube, Google Maps
 * ------------------------------------------------------------------ */

/** Nomor WhatsApp → format internasional (62812...) */
function wa_number(?string $raw): string
{
    $d = preg_replace('/\D+/', '', (string) $raw);
    if ($d === '') {
        return '';
    }
    if (strpos($d, '00') === 0) {
        $d = substr($d, 2);
    }
    if (strpos($d, '0') === 0) {
        $d = '62' . substr($d, 1);
    } elseif (strpos($d, '8') === 0) {
        $d = '62' . $d;
    }
    return $d;
}

function wa_link(?string $raw, string $message = ''): string
{
    $n = wa_number($raw);
    if ($n === '') {
        return '';
    }
    return 'https://wa.me/' . $n . ($message !== '' ? '?text=' . rawurlencode($message) : '');
}

function youtube_id(?string $url): ?string
{
    $url = trim((string) $url);
    if ($url === '') {
        return null;
    }
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/))([A-Za-z0-9_-]{11})~', $url, $m)) {
        return $m[1];
    }
    return null;
}

/** Ambil alamat src dari kode embed Google Maps (boleh tempel seluruh <iframe>) */
function maps_embed_src(?string $value): ?string
{
    $v = trim((string) $value);
    if ($v === '') {
        return null;
    }
    if (stripos($v, '<iframe') !== false && preg_match('/src=["\']([^"\']+)["\']/i', $v, $m)) {
        $v = html_entity_decode($m[1]);
    }
    if (preg_match('~^https://(www\.)?google\.com/maps/embed\?~i', $v) || preg_match('~^https://maps\.google\.com/~i', $v)) {
        return $v;
    }
    return null;
}

/* ------------------------------------------------------------------
 * Produk
 * ------------------------------------------------------------------ */

/** [label, nama class css] untuk status produk */
function product_status(?string $status): array
{
    $map = [
        'tersedia' => ['Tersedia', 'available'],
        'dipesan'  => ['Dipesan', 'reserved'],
        'terjual'  => ['Terjual', 'sold'],
    ];
    return $map[$status] ?? $map['tersedia'];
}

function video_mime(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $map = ['mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogv' => 'video/ogg', 'mov' => 'video/mp4'];
    return $map[$ext] ?? 'video/mp4';
}

/* ------------------------------------------------------------------
 * Pagination (nomor halaman)
 * ------------------------------------------------------------------ */

function pagination_html(int $page, int $pages, string $path, array $query = []): string
{
    if ($pages <= 1) {
        return '';
    }
    unset($query['page']);
    $link = function (int $p) use ($path, $query) {
        $q = $query;
        if ($p > 1) {
            $q['page'] = $p;
        }
        $qs = http_build_query(array_filter($q, function ($v) {
            return $v !== '' && $v !== null && $v !== 0;
        }));
        return url($path) . ($qs !== '' ? '?' . $qs : '');
    };
    $html = '<nav aria-label="Halaman"><ul class="pagination justify-content-center flex-wrap">';
    $html .= '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '"><a class="page-link" href="' . e($link(max(1, $page - 1))) . '">&laquo;</a></li>';
    $start = max(1, $page - 2);
    $end = min($pages, $page + 2);
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($link(1)) . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
    }
    for ($i = $start; $i <= $end; $i++) {
        $html .= '<li class="page-item ' . ($i === $page ? 'active' : '') . '"><a class="page-link" href="' . e($link($i)) . '">' . $i . '</a></li>';
    }
    if ($end < $pages) {
        if ($end < $pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . e($link($pages)) . '">' . $pages . '</a></li>';
    }
    $html .= '<li class="page-item ' . ($page >= $pages ? 'disabled' : '') . '"><a class="page-link" href="' . e($link(min($pages, $page + 1))) . '">&raquo;</a></li>';
    return $html . '</ul></nav>';
}

/* ------------------------------------------------------------------
 * Ikon (SVG kecil, tanpa perlu internet / library tambahan)
 * ------------------------------------------------------------------ */

/** Menampilkan ikon: icon('whatsapp'), icon('instagram', 24) */
function icon(string $name, int $size = 18): string
{
    static $paths = [
        'whatsapp'  => 'M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.2 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.8s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.1.1.3 0 .5l-.3.4-.4.5c-.1.1-.3.3-.1.6.2.3.7 1.2 1.5 1.9 1 .9 1.9 1.2 2.2 1.3.3.1.4.1.6-.1l.8-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.8-.1 1.4z',
        'instagram' => 'M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 2a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM17.3 5.7a1 1 0 1 1 0 2 1 1 0 0 1 0-2z',
        'facebook'  => 'M13.5 22v-8.2h2.8l.5-3.3h-3.3V8.4c0-.9.3-1.6 1.7-1.6h1.7V3.9c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.4H7.4v3.3h2.8V22h3.3z',
        'tiktok'    => 'M16.6 2h-3.2v13.2a2.9 2.9 0 1 1-2.9-2.9c.3 0 .6 0 .9.1V9.1a6.2 6.2 0 1 0 5.2 6.1V8.7a7.6 7.6 0 0 0 4.4 1.4V6.9a4.4 4.4 0 0 1-4.4-4.9z',
        'youtube'   => 'M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3L10 15z',
        'phone'     => 'M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1l-2.3 2.2z',
        'mail'      => 'M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm1.5 2L12 12.5 19.5 7h-15zM4 9.2V17h16V9.2l-8 5.8-8-5.8z',
        'pin'       => 'M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7zm0 4.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z',
        'clock'     => 'M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20zm-1 5v6l4.5 2.7.8-1.3-3.8-2.2V7H11z',
        'play'      => 'M8 5v14l11-7L8 5z',
        'eye'       => 'M12 5c5 0 9 4.5 10 7-1 2.5-5 7-10 7S3 14.500 2 12c1-2.500 5-7 10-7zm0 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8z',
        'check'     => 'M9 16.2 4.800 12l-1.400 1.400L9 19 21 7l-1.400-1.400L9 16.200z',
        'search'    => 'M10 2a8 8 0 0 1 6.300 12.900l5.400 5.400-1.400 1.400-5.400-5.400A8 8 0 1 1 10 2zm0 2a6 6 0 1 0 0 12 6 6 0 0 0 0-12z',
        'menu'      => 'M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z',
        'plus'      => 'M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z',
        'edit'      => 'M16.900 3.500a2 2 0 0 1 2.800 0l.8.800a2 2 0 0 1 0 2.800L8.500 19.100 3 21l1.900-5.500L16.900 3.500z',
        'trash'     => 'M9 3h6l1 2h4v2H4V5h4l1-2zM5 9h14l-1 12H6L5 9z',
        'home'      => 'M12 3 2 12h3v8h5v-6h4v6h5v-8h3L12 3z',
        'box'       => 'M12 2 3 6.500v11L12 22l9-4.500v-11L12 2zm0 2.200 5.800 2.900L12 10 6.200 7.100 12 4.200zM5 8.700l6 3v7.600l-6-3V8.700zm14 0v7.600l-6 3v-7.600l6-3z',
        'tag'       => 'M3 3h8l10 10-8 8L3 11V3zm4 2.500a1.500 1.500 0 1 0 0 3 1.500 1.500 0 0 0 0-3z',
        'store'     => 'M4 4h16l2 6a3 3 0 0 1-5 2 3 3 0 0 1-5 0 3 3 0 0 1-5 0 3 3 0 0 1-5-2l2-6zm1 10h2v6h10v-6h2v8H5v-8z',
        'gear'      => 'M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm-1-6h2l.5 2.500 1.800.8 2.200-1.400 1.400 1.400-1.400 2.200.8 1.800L22 11v2l-2.500.5-.8 1.800 1.400 2.200-1.400 1.400-2.200-1.400-1.800.8L13 22h-2l-.5-2.500-1.800-.8-2.200 1.400-1.400-1.400 1.400-2.200-.8-1.800L2 13v-2l2.500-.5.8-1.800-1.400-2.200 1.400-1.400 2.200 1.400 1.800-.8L11 2z',
        'user'      => 'M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4.400 0-8 2.200-8 5v3h16v-3c0-2.800-3.600-5-8-5z',
        'logout'    => 'M10 3h9a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-9v-2h9V5h-9V3zM8 7l-5 5 5 5v-3.500h8v-3H8V7z',
        'external'  => 'M14 3h7v7h-2V6.400l-8.300 8.300-1.400-1.400L17.600 5H14V3zM5 5h6v2H5v12h12v-6h2v8H3V5h2z',
        'image'     => 'M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1zm3 4a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-2 10h14v-3.500L15 10l-5 5-2-2-3 3.500V18z',
        'chat'      => 'M4 3h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H8l-5 4V4a1 1 0 0 1 1-1z',
    ];
    $d = $paths[$name] ?? $paths['box'];
    return '<svg class="icon icon-' . e($name) . '" width="' . $size . '" height="' . $size
        . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="' . $d . '"/></svg>';
}
