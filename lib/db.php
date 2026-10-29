<?php

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = (string) config('db_host', '127.0.0.1');
    $port = (string) config('db_port', '3306');
    $name = (string) config('db_name', 'ppdb_smkspp');
    $user = (string) config('db_user', 'root');
    $pass = (string) config('db_pass', '');
    $dsnBase = "mysql:host={$host};port={$port};charset=utf8mb4";

    try {
        $root = new PDO($dsnBase, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $safe = str_replace('`', '', $name);
        $root->exec("CREATE DATABASE IF NOT EXISTS `{$safe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $root->exec("USE `{$safe}`");
        $pdo = $root;
    } catch (Throwable $e) {
        http_response_code(500);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Database</title></head><body style="font-family:sans-serif;max-width:640px;margin:3rem auto;line-height:1.5">';
        echo '<h1>MySQL belum terhubung</h1>';
        echo '<p>Sistem PPDB membutuhkan MySQL. Pastikan XAMPP MySQL sudah dijalankan, lalu periksa <code>config/app.php</code>.</p>';
        echo '<p><strong>Pesan:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</body></html>';
        exit;
    }

    migrate($pdo);
    seed($pdo);
    return $pdo;
}

function migrate(PDO $pdo): void
{
    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(64) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            display_name VARCHAR(120) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS settings (
            setting_key VARCHAR(64) PRIMARY KEY,
            setting_value TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nomor VARCHAR(32) UNIQUE NOT NULL,
            tahun_ajaran VARCHAR(20) NOT NULL,
            nama VARCHAR(120) NOT NULL,
            nisn VARCHAR(10) UNIQUE NOT NULL,
            nik VARCHAR(16) UNIQUE NOT NULL,
            tempat_lahir VARCHAR(80) NOT NULL,
            tanggal_lahir DATE NOT NULL,
            jenis_kelamin ENUM('L','P') NOT NULL,
            agama VARCHAR(20) NOT NULL,
            alamat TEXT NOT NULL,
            no_hp VARCHAR(20) NOT NULL,
            email VARCHAR(120) NULL,
            asal_sekolah VARCHAR(160) NOT NULL,
            jurusan VARCHAR(160) NOT NULL,
            nama_ayah VARCHAR(120) NOT NULL,
            pekerjaan_ayah VARCHAR(80) NOT NULL,
            nama_ibu VARCHAR(120) NOT NULL,
            pekerjaan_ibu VARCHAR(80) NOT NULL,
            no_hp_ortu VARCHAR(20) NOT NULL,
            status VARCHAR(32) NOT NULL DEFAULT 'menunggu',
            catatan_admin TEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX idx_status (status),
            INDEX idx_jurusan (jurusan)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            registration_id INT NOT NULL,
            doc_type VARCHAR(32) NOT NULL,
            original_name VARCHAR(180) NOT NULL,
            stored_name VARCHAR(180) NOT NULL,
            mime VARCHAR(80) NOT NULL,
            size_bytes INT NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE KEY uniq_reg_type (registration_id, doc_type),
            CONSTRAINT fk_doc_reg FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            registration_id INT NULL,
            channel VARCHAR(20) NOT NULL,
            target VARCHAR(40) NOT NULL,
            title VARCHAR(160) NOT NULL,
            message TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'queued',
            provider_response TEXT NULL,
            created_at DATETIME NOT NULL,
            sent_at DATETIME NULL,
            INDEX idx_created (created_at),
            CONSTRAINT fk_notif_reg FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    SQL);
}

function seed(PDO $pdo): void
{
    $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare('INSERT INTO users (username, password_hash, display_name) VALUES (?,?,?)')
            ->execute(['admin', $hash, 'Panitia PPDB']);
    }

    $defaults = [
        'school_name' => (string) config('school_name'),
        'school_address' => 'Stabat, Kabupaten Langkat, Sumatera Utara',
        'school_phone' => '0821-0000-0000',
        'school_email' => 'ppdb@smksppputrajayastabat.sch.id',
        'tahun_ajaran' => '2026/2027',
        'pendaftaran_buka' => '1',
        'wa_enabled' => '0',
        'wa_token' => '',
        'wa_admin' => '082100000000',
        'wa_provider' => 'fonnte',
        'info_singkat' => 'SMKS-PP Putra Jaya Stabat membuka pendaftaran peserta didik baru. Lengkapi data, unggah berkas, lalu pantau status secara daring. Notifikasi juga dikirim ke WhatsApp.',
    ];
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)'
    );
    foreach ($defaults as $k => $v) {
        $stmt->execute([$k, $v]);
    }
}
