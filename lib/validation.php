<?php

function registration_input(array $post): array
{
    $jk = ($post['jenis_kelamin'] ?? '') === 'P' ? 'P' : (($post['jenis_kelamin'] ?? '') === 'L' ? 'L' : '');
    $agama = (string) ($post['agama'] ?? '');
    if (!in_array($agama, agama_list(), true)) {
        $agama = '';
    }
    $jurusan = (string) ($post['jurusan'] ?? '');
    if (!in_array($jurusan, jurusan_list(), true)) {
        $jurusan = '';
    }

    return [
        'nama' => trim((string) ($post['nama'] ?? '')),
        'nisn' => preg_replace('/\D+/', '', (string) ($post['nisn'] ?? '')) ?? '',
        'nik' => preg_replace('/\D+/', '', (string) ($post['nik'] ?? '')) ?? '',
        'tempat_lahir' => trim((string) ($post['tempat_lahir'] ?? '')),
        'tanggal_lahir' => trim((string) ($post['tanggal_lahir'] ?? '')),
        'jenis_kelamin' => $jk,
        'agama' => $agama,
        'alamat' => trim((string) ($post['alamat'] ?? '')),
        'no_hp' => trim((string) ($post['no_hp'] ?? '')),
        'email' => trim((string) ($post['email'] ?? '')),
        'asal_sekolah' => trim((string) ($post['asal_sekolah'] ?? '')),
        'jurusan' => $jurusan,
        'nama_ayah' => trim((string) ($post['nama_ayah'] ?? '')),
        'pekerjaan_ayah' => trim((string) ($post['pekerjaan_ayah'] ?? '')),
        'nama_ibu' => trim((string) ($post['nama_ibu'] ?? '')),
        'pekerjaan_ibu' => trim((string) ($post['pekerjaan_ibu'] ?? '')),
        'no_hp_ortu' => trim((string) ($post['no_hp_ortu'] ?? '')),
    ];
}

function validate_registration(array $data, array $files, bool $requireDocs = true): array
{
    $errors = [];
    $required = [
        'nama' => 'Nama lengkap',
        'nisn' => 'NISN',
        'nik' => 'NIK',
        'tempat_lahir' => 'Tempat lahir',
        'tanggal_lahir' => 'Tanggal lahir',
        'jenis_kelamin' => 'Jenis kelamin',
        'agama' => 'Agama',
        'alamat' => 'Alamat',
        'no_hp' => 'Nomor WhatsApp siswa',
        'asal_sekolah' => 'Asal sekolah',
        'jurusan' => 'Jurusan',
        'nama_ayah' => 'Nama ayah',
        'pekerjaan_ayah' => 'Pekerjaan ayah',
        'nama_ibu' => 'Nama ibu',
        'pekerjaan_ibu' => 'Pekerjaan ibu',
        'no_hp_ortu' => 'Nomor HP orang tua',
    ];
    foreach ($required as $key => $label) {
        if ($data[$key] === '') {
            $errors[$key] = $label . ' wajib diisi.';
        }
    }

    if ($data['nama'] !== '' && (mb_strlen($data['nama']) < 3 || !preg_match('/^[\p{L}\s.\'-]+$/u', $data['nama']))) {
        $errors['nama'] = 'Nama hanya boleh huruf dan minimal 3 karakter.';
    }
    if ($data['nisn'] !== '' && !preg_match('/^\d{10}$/', $data['nisn'])) {
        $errors['nisn'] = 'NISN harus 10 digit angka.';
    }
    if ($data['nik'] !== '' && !preg_match('/^\d{16}$/', $data['nik'])) {
        $errors['nik'] = 'NIK harus 16 digit angka.';
    }
    if ($data['tanggal_lahir'] !== '') {
        $dt = DateTime::createFromFormat('Y-m-d', $data['tanggal_lahir']);
        $ok = $dt && $dt->format('Y-m-d') === $data['tanggal_lahir'];
        if (!$ok) {
            $errors['tanggal_lahir'] = 'Tanggal lahir tidak valid.';
        } else {
            $age = (int) $dt->diff(new DateTime('today'))->y;
            if ($age < 12 || $age > 22) {
                $errors['tanggal_lahir'] = 'Usia calon siswa harus antara 12–22 tahun.';
            }
        }
    }
    if ($data['no_hp'] !== '' && !preg_match('/^(\+?62|0)8\d{8,12}$/', preg_replace('/\s+/', '', $data['no_hp']) ?? '')) {
        $errors['no_hp'] = 'Nomor WhatsApp tidak valid. Gunakan format 08xxxxxxxxxx.';
    }
    if ($data['no_hp_ortu'] !== '' && !preg_match('/^(\+?62|0)8\d{8,12}$/', preg_replace('/\s+/', '', $data['no_hp_ortu']) ?? '')) {
        $errors['no_hp_ortu'] = 'Nomor HP orang tua tidak valid.';
    }
    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email tidak valid.';
    }

    if ($data['nisn'] !== '') {
        $st = db()->prepare('SELECT id FROM registrations WHERE nisn = ?');
        $st->execute([$data['nisn']]);
        if ($st->fetch()) {
            $errors['nisn'] = 'NISN sudah terdaftar.';
        }
    }
    if ($data['nik'] !== '') {
        $st = db()->prepare('SELECT id FROM registrations WHERE nik = ?');
        $st->execute([$data['nik']]);
        if ($st->fetch()) {
            $errors['nik'] = 'NIK sudah terdaftar.';
        }
    }

    if ($requireDocs) {
        $errors = array_merge($errors, validate_documents($files));
    }

    return $errors;
}

function validate_documents(array $files): array
{
    $errors = [];
    $max = ((int) config('upload_max_mb', 2)) * 1024 * 1024;
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
    ];

    foreach (document_types() as $type => $label) {
        $info = $files[$type] ?? null;
        if (!$info || (int) ($info['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $errors[$type] = $label . ' wajib diunggah.';
            continue;
        }
        if ((int) $info['error'] !== UPLOAD_ERR_OK) {
            $errors[$type] = $label . ' gagal diunggah.';
            continue;
        }
        if ((int) $info['size'] > $max) {
            $errors[$type] = $label . ' maksimal ' . config('upload_max_mb') . ' MB.';
            continue;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($info['tmp_name']) ?: '';
        if (!isset($allowed[$mime])) {
            $errors[$type] = $label . ' harus JPG, PNG, atau PDF.';
        }
    }
    return $errors;
}

function next_nomor(string $tahunAjaran): string
{
    $year = preg_replace('/\D+/', '', substr($tahunAjaran, 0, 4)) ?: date('Y');
    $prefix = 'PJ' . $year;
    $stmt = db()->prepare("SELECT nomor FROM registrations WHERE nomor LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last = (string) $stmt->fetchColumn();
    $seq = 1;
    if ($last !== '' && preg_match('/(\d{4})$/', $last, $m)) {
        $seq = (int) $m[1] + 1;
    }
    return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
}
