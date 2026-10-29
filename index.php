<?php

require __DIR__ . '/bootstrap.php';

$page = $_GET['page'] ?? 'beranda';
$public = ['beranda', 'daftar', 'status', 'sukses', 'login'];
$admin = ['dashboard', 'pendaftar', 'detail', 'notifikasi', 'pengaturan'];
$allowed = array_merge($public, $admin, ['logout', 'excel', 'berkas']);
if (!in_array($page, $allowed, true)) {
    $page = 'beranda';
}

if ($page === 'logout') {
    logout();
    redirect('login');
}

if ($page === 'excel') {
    require_login();
    $path = sync_excel_export();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="rekap-ppdb-smkspp.xlsx"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

if ($page === 'berkas') {
    require_login();
    $id = (int) ($_GET['id'] ?? 0);
    $st = db()->prepare(
        'SELECT d.*, r.nomor FROM documents d JOIN registrations r ON r.id = d.registration_id WHERE d.id = ?'
    );
    $st->execute([$id]);
    $doc = $st->fetch();
    if (!$doc) {
        http_response_code(404);
        exit('Berkas tidak ditemukan.');
    }
    $path = upload_dir() . DIRECTORY_SEPARATOR . preg_replace('/[^A-Z0-9]/', '', $doc['nomor']) . DIRECTORY_SEPARATOR . $doc['stored_name'];
    if (!is_file($path)) {
        http_response_code(404);
        exit('File tidak ada di server.');
    }
    header('Content-Type: ' . $doc['mime']);
    header('Content-Disposition: inline; filename="' . basename($doc['original_name']) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    csrf_check();

    if ($action === 'login') {
        $ok = attempt_login(trim((string) $_POST['username']), (string) $_POST['password']);
        if ($ok) {
            redirect('dashboard');
        }
        flash('error', 'Username atau sandi salah.');
        redirect('login');
    }

    if ($action === 'daftar') {
        if (setting('pendaftaran_buka', '1') !== '1') {
            flash('error', 'Pendaftaran sedang ditutup.');
            redirect('daftar');
        }
        $data = registration_input($_POST);
        $files = [];
        foreach (array_keys(document_types()) as $type) {
            $files[$type] = $_FILES[$type] ?? ['error' => UPLOAD_ERR_NO_FILE, 'tmp_name' => '', 'name' => '', 'size' => 0];
        }
        $errors = validate_registration($data, $files, true);
        if ($errors) {
            $_SESSION['form'] = $data;
            $_SESSION['form_errors'] = $errors;
            flash('error', 'Periksa kembali data dan berkas yang ditandai.');
            redirect('daftar');
        }

        $tahun = setting('tahun_ajaran', '2026/2027');
        $nomor = next_nomor($tahun);
        $now = date('Y-m-d H:i:s');
        db()->beginTransaction();
        try {
            db()->prepare(
                'INSERT INTO registrations (
                    nomor, tahun_ajaran, nama, nisn, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, agama,
                    alamat, no_hp, email, asal_sekolah, jurusan, nama_ayah, pekerjaan_ayah, nama_ibu,
                    pekerjaan_ibu, no_hp_ortu, status, created_at, updated_at
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            )->execute([
                $nomor, $tahun, $data['nama'], $data['nisn'], $data['nik'], $data['tempat_lahir'],
                $data['tanggal_lahir'], $data['jenis_kelamin'], $data['agama'], $data['alamat'],
                $data['no_hp'], $data['email'] ?: null, $data['asal_sekolah'], $data['jurusan'],
                $data['nama_ayah'], $data['pekerjaan_ayah'], $data['nama_ibu'], $data['pekerjaan_ibu'],
                $data['no_hp_ortu'], 'menunggu', $now, $now,
            ]);
            $id = (int) db()->lastInsertId();
            save_documents($id, $nomor, $files);
            db()->commit();
        } catch (Throwable $e) {
            db()->rollBack();
            $_SESSION['form'] = $data;
            flash('error', 'Pendaftaran gagal disimpan. Coba lagi.');
            redirect('daftar');
        }

        $st = db()->prepare('SELECT * FROM registrations WHERE id = ?');
        $st->execute([$id]);
        $reg = $st->fetch();
        $notif = dispatch_registration_notifications($reg);
        try {
            sync_excel_export();
        } catch (Throwable $e) {
            // Excel sync should not block success
        }
        unset($_SESSION['form'], $_SESSION['form_errors']);
        $_SESSION['sukses'] = [
            'nomor' => $nomor,
            'nama' => $reg['nama'],
            'wa_link' => $notif['siswa_link'],
            'wa_api' => (bool) ($notif['siswa']['ok'] ?? false),
        ];
        redirect('sukses');
    }

    if ($action === 'cek_status') {
        $nomor = strtoupper(trim((string) ($_POST['nomor'] ?? '')));
        $nisn = preg_replace('/\D+/', '', (string) ($_POST['nisn'] ?? '')) ?? '';
        $st = db()->prepare('SELECT * FROM registrations WHERE nomor = ? AND nisn = ?');
        $st->execute([$nomor, $nisn]);
        $found = $st->fetch();
        if (!$found) {
            flash('error', 'Data tidak ditemukan. Periksa nomor pendaftaran dan NISN.');
            redirect('status');
        }
        $_SESSION['status_result'] = $found;
        redirect('status');
    }

    require_login();
    $user = current_user();

    if ($action === 'update_status') {
        $id = (int) ($_POST['id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');
        if (!isset(status_list()[$status])) {
            flash('error', 'Status tidak valid.');
            redirect('detail', ['id' => $id]);
        }
        $catatan = trim((string) ($_POST['catatan_admin'] ?? ''));
        db()->prepare('UPDATE registrations SET status=?, catatan_admin=?, updated_at=? WHERE id=?')
            ->execute([$status, $catatan, date('Y-m-d H:i:s'), $id]);
        $st = db()->prepare('SELECT * FROM registrations WHERE id = ?');
        $st->execute([$id]);
        $reg = $st->fetch();
        if ($reg) {
            dispatch_status_whatsapp($reg);
            try {
                sync_excel_export();
            } catch (Throwable $e) {
            }
        }
        flash('ok', 'Status diperbarui dan notifikasi WhatsApp diproses.');
        redirect('detail', ['id' => $id]);
    }

    if ($action === 'kirim_wa') {
        $id = (int) ($_POST['id'] ?? 0);
        $st = db()->prepare('SELECT * FROM registrations WHERE id = ?');
        $st->execute([$id]);
        $reg = $st->fetch();
        if ($reg) {
            dispatch_status_whatsapp($reg);
            flash('ok', 'Notifikasi WhatsApp dikirim ulang / tautan disiapkan.');
        }
        redirect('detail', ['id' => $id]);
    }

    if ($action === 'save_settings') {
        $keys = [
            'school_name', 'school_address', 'school_phone', 'school_email', 'tahun_ajaran',
            'info_singkat', 'wa_admin', 'wa_token', 'wa_provider',
        ];
        foreach ($keys as $key) {
            setting_set($key, trim((string) ($_POST[$key] ?? '')));
        }
        setting_set('pendaftaran_buka', isset($_POST['pendaftaran_buka']) ? '1' : '0');
        setting_set('wa_enabled', isset($_POST['wa_enabled']) ? '1' : '0');
        flash('ok', 'Pengaturan disimpan.');
        redirect('pengaturan');
    }

    if ($action === 'change_password') {
        $err = change_password((int) $user['id'], (string) $_POST['old_password'], (string) $_POST['new_password']);
        if ($err) {
            flash('error', $err);
        } else {
            flash('ok', 'Sandi admin berhasil diubah.');
        }
        redirect('pengaturan');
    }
}

if (in_array($page, $admin, true)) {
    require_login();
}

if ($page === 'login' && current_user() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard');
}

$user = current_user();
$schoolName = setting('school_name', (string) config('school_name'));
$titleMap = [
    'beranda' => 'Pendaftaran Siswa Baru',
    'daftar' => 'Form Pendaftaran',
    'status' => 'Cek Status',
    'sukses' => 'Pendaftaran Berhasil',
    'login' => 'Masuk Admin',
    'dashboard' => 'Dashboard Rekap',
    'pendaftar' => 'Data Pendaftar',
    'detail' => 'Validasi Berkas',
    'notifikasi' => 'Notifikasi',
    'pengaturan' => 'Pengaturan',
];
$pageTitle = $titleMap[$page] ?? 'PPDB';
$view = __DIR__ . '/views/' . $page . '.php';

$isPublic = in_array($page, ['beranda', 'daftar', 'status', 'sukses'], true);
if ($isPublic) {
    require __DIR__ . '/views/public-layout.php';
} else {
    require __DIR__ . '/views/layout.php';
}
