<?php

function config(?string $key = null, mixed $default = null): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require dirname(__DIR__) . '/config/app.php';
    }
    if ($key === null) {
        return $cfg;
    }
    return $cfg[$key] ?? $default;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $page, array $query = []): void
{
    $query['page'] = $page;
    header('Location: ?' . http_build_query($query));
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = (string) ($_POST['_csrf'] ?? '');
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Token tidak valid. Muat ulang halaman.');
    }
}

function setting(string $key, ?string $default = null): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll() as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return (string) ($cache[$key] ?? $default ?? '');
}

function setting_set(string $key, string $value): void
{
    $stmt = db()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
}

function jurusan_list(): array
{
    return [
        'Perkebunan',
        'Peternakan',
    ];
}

function agama_list(): array
{
    return ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
}

function status_list(): array
{
    return [
        'menunggu' => 'Menunggu verifikasi',
        'berkas_kurang' => 'Berkas perlu dilengkapi',
        'diverifikasi' => 'Berkas terverifikasi',
        'diterima' => 'Diterima',
        'cadangan' => 'Cadangan',
        'ditolak' => 'Tidak diterima',
    ];
}

function status_label(string $status): string
{
    return status_list()[$status] ?? $status;
}

function status_badge_class(string $status): string
{
    return match ($status) {
        'diterima', 'diverifikasi' => 'ok',
        'ditolak' => 'no',
        'berkas_kurang', 'cadangan' => 'wait',
        default => 'info',
    };
}

function document_types(): array
{
    return [
        'foto' => 'Pas foto 3x4',
        'kk' => 'Kartu Keluarga',
        'akta' => 'Akta kelahiran',
        'ijazah' => 'Ijazah / SKL',
        'rapor' => 'Rapor terakhir',
        'ktp_ortu' => 'KTP orang tua / wali',
    ];
}

function normalize_phone(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (str_starts_with($digits, '0')) {
        $digits = '62' . substr($digits, 1);
    }
    if ($digits !== '' && !str_starts_with($digits, '62')) {
        $digits = '62' . $digits;
    }
    return $digits;
}

function format_date_id(?string $date): string
{
    if (!$date) {
        return '-';
    }
    $ts = strtotime($date);
    if (!$ts) {
        return $date;
    }
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

function data_dir(): string
{
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    return $dir;
}

function upload_dir(): string
{
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $deny = $dir . DIRECTORY_SEPARATOR . '.htaccess';
    if (!is_file($deny)) {
        file_put_contents($deny, "Require all denied\n");
    }
    return $dir;
}
