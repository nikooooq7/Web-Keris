<?php
/**
 * UPLOAD — menyimpan foto & video dengan aman.
 *
 * - Jenis file dicek dari ISI file (bukan hanya nama/ekstensi).
 * - Nama file diganti acak agar aman.
 * - Foto besar otomatis dikecilkan (jika ekstensi GD aktif) dan data lokasi (EXIF) dihapus.
 *
 * Semua fungsi melempar RuntimeException berisi pesan bahasa Indonesia jika gagal.
 */
class Upload
{
    const IMAGE_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    const VIDEO_TYPES = [
        'video/mp4'       => 'mp4',
        'video/webm'      => 'webm',
        'video/ogg'       => 'ogv',
        'video/quicktime' => 'mov',
    ];

    /** Mengubah $_FILES['nama'] (satu atau banyak file) menjadi daftar file yang rapi */
    public static function multiple(array $files): array
    {
        $out = [];
        if (!isset($files['name'])) {
            return $out;
        }
        if (!is_array($files['name'])) {
            return ($files['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE ? [] : [$files];
        }
        foreach ($files['name'] as $i => $name) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = [
                'name'     => $name,
                'type'     => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i] ?? 0,
            ];
        }
        return $out;
    }

    /** Simpan satu foto. Mengembalikan path relatif (mis. "products/ab12.jpg") atau null jika tidak ada file. */
    public static function image(array $file, string $folder, int $maxDim = 1600): ?string
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        $name = (string) ($file['name'] ?? 'foto');
        self::checkError($file);

        $maxMb = (int) app_config('upload.max_image_mb', 10);
        if ($file['size'] > $maxMb * 1048576) {
            throw new RuntimeException('Ukuran foto "' . $name . '" melebihi ' . $maxMb . ' MB.');
        }
        $tmp = $file['tmp_name'];
        if (!is_uploaded_file($tmp)) {
            throw new RuntimeException('File "' . $name . '" tidak valid.');
        }

        $info = @getimagesize($tmp);
        $mime = self::mime($tmp);
        if (!$info || !isset(self::IMAGE_TYPES[$mime])) {
            throw new RuntimeException('Format foto "' . $name . '" tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.');
        }

        $dir = UPLOAD_PATH . '/' . $folder;
        self::ensureDir($dir);
        $filename = bin2hex(random_bytes(8)) . '.' . self::IMAGE_TYPES[$mime];
        $dest = $dir . '/' . $filename;

        if (!self::processImage($tmp, $dest, $mime, $info, $maxDim)) {
            if (!move_uploaded_file($tmp, $dest)) {
                throw new RuntimeException('Gagal menyimpan "' . $name . '". Pastikan folder public/uploads dapat ditulis (izin 755).');
            }
        }
        @chmod($dest, 0644);
        return $folder . '/' . $filename;
    }

    /** Simpan satu video. Mengembalikan path relatif atau null jika tidak ada file. */
    public static function video(array $file, string $folder): ?string
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        $name = (string) ($file['name'] ?? 'video');
        self::checkError($file);

        $maxMb = (int) app_config('upload.max_video_mb', 100);
        if ($file['size'] > $maxMb * 1048576) {
            throw new RuntimeException('Ukuran video "' . $name . '" melebihi ' . $maxMb . ' MB. Gunakan tautan YouTube untuk video besar.');
        }
        $tmp = $file['tmp_name'];
        if (!is_uploaded_file($tmp)) {
            throw new RuntimeException('File "' . $name . '" tidak valid.');
        }

        $mime = self::mime($tmp);
        $extFromName = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $ext = self::VIDEO_TYPES[$mime] ?? null;
        // Beberapa server mendeteksi video sebagai tipe umum; dalam kasus itu pakai ekstensi nama file
        if ($ext === null && in_array($mime, ['', 'application/octet-stream', 'video/x-matroska', 'video/x-m4v'], true)
            && in_array($extFromName, ['mp4', 'webm', 'ogv', 'mov', 'm4v'], true)) {
            $ext = $extFromName === 'm4v' ? 'mp4' : $extFromName;
        }
        if ($ext === null) {
            throw new RuntimeException('Format video "' . $name . '" tidak didukung. Gunakan MP4 (disarankan), WEBM, atau OGV.');
        }

        $dir = UPLOAD_PATH . '/' . $folder;
        self::ensureDir($dir);
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($tmp, $dir . '/' . $filename)) {
            throw new RuntimeException('Gagal menyimpan video "' . $name . '". Pastikan folder public/uploads dapat ditulis (izin 755).');
        }
        @chmod($dir . '/' . $filename, 0644);
        return $folder . '/' . $filename;
    }

    /** Hapus file hasil upload (aman: hanya di dalam public/uploads) */
    public static function delete(?string $relative): void
    {
        if (!$relative || strpos($relative, '..') !== false || strpos($relative, 'assets/') === 0) {
            return;
        }
        $file = UPLOAD_PATH . '/' . ltrim($relative, '/');
        if (is_file($file)) {
            @unlink($file);
        }
    }

    /* -------------------------------------------------------------- */

    private static function checkError(array $file): void
    {
        $name = (string) ($file['name'] ?? 'file');
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                return;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('File "' . $name . '" terlalu besar. Batas upload server saat ini ' . upload_limit_text()
                    . ' (lihat PANDUAN.md untuk cara menaikkannya).');
            case UPLOAD_ERR_PARTIAL:
                throw new RuntimeException('File "' . $name . '" hanya terunggah sebagian. Silakan coba lagi.');
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
                throw new RuntimeException('Server tidak dapat menyimpan file sementara. Hubungi penyedia hosting Anda.');
            default:
                throw new RuntimeException('Upload "' . $name . '" gagal (kode ' . $file['error'] . ').');
        }
    }

    private static function mime(string $tmp): string
    {
        if (class_exists('finfo')) {
            $f = new finfo(FILEINFO_MIME_TYPE);
            $m = $f->file($tmp);
            if ($m) {
                return $m;
            }
        }
        if (function_exists('mime_content_type')) {
            $m = @mime_content_type($tmp);
            if ($m) {
                return $m;
            }
        }
        $i = @getimagesize($tmp);
        return ($i && !empty($i['mime'])) ? $i['mime'] : '';
    }

    private static function ensureDir(string $dir): void
    {
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Folder upload tidak dapat dibuat. Pastikan folder public/uploads dapat ditulis (izin 755).');
        }
    }

    /**
     * Mengecilkan / merapikan foto memakai GD. Mengembalikan false jika tidak bisa
     * (lalu file asli disimpan apa adanya).
     */
    private static function processImage(string $tmp, string $dest, string $mime, array $info, int $maxDim): bool
    {
        if (!function_exists('imagecreatetruecolor') || $mime === 'image/gif') {
            return false;
        }
        $w = (int) $info[0];
        $h = (int) $info[1];
        $needsResize = ($w > $maxDim || $h > $maxDim);
        if (!$needsResize && $mime !== 'image/jpeg') {
            return false; // PNG/WEBP kecil disimpan apa adanya
        }
        // Batasi agar tidak kehabisan memori pada foto raksasa
        if ($w * $h > 40000000) {
            return false;
        }

        $orientation = 1;
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $ex = @exif_read_data($tmp);
            $orientation = (int) ($ex['Orientation'] ?? 1);
        }

        switch ($mime) {
            case 'image/jpeg':
                $src = @imagecreatefromjpeg($tmp);
                break;
            case 'image/png':
                $src = @imagecreatefrompng($tmp);
                break;
            case 'image/webp':
                $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : false;
                break;
            default:
                $src = false;
        }
        if (!$src) {
            return false;
        }

        if ($needsResize) {
            $ratio = min($maxDim / $w, $maxDim / $h);
            $nw = max(1, (int) round($w * $ratio));
            $nh = max(1, (int) round($h * $ratio));
            $dst = imagecreatetruecolor($nw, $nh);
            if ($mime !== 'image/jpeg') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($src);
            $src = $dst;
        }

        if ($orientation > 1) {
            $rot = null;
            if ($orientation === 3) {
                $rot = imagerotate($src, 180, 0);
            } elseif ($orientation === 6) {
                $rot = imagerotate($src, -90, 0);
            } elseif ($orientation === 8) {
                $rot = imagerotate($src, 90, 0);
            }
            if ($rot) {
                imagedestroy($src);
                $src = $rot;
            }
        }

        switch ($mime) {
            case 'image/jpeg':
                imageinterlace($src, true);
                $ok = imagejpeg($src, $dest, 85);
                break;
            case 'image/png':
                $ok = imagepng($src, $dest, 6);
                break;
            case 'image/webp':
                $ok = function_exists('imagewebp') ? imagewebp($src, $dest, 85) : false;
                break;
            default:
                $ok = false;
        }
        imagedestroy($src);
        if (!$ok && is_file($dest)) {
            @unlink($dest);
        }
        return (bool) $ok;
    }
}
