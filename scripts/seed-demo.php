<?php
require __DIR__ . '/../bootstrap.php';

$now = date('Y-m-d H:i:s');
$exists = db()->query("SELECT id FROM registrations WHERE nisn = '1234567890'")->fetch();
if ($exists) {
    echo "Demo already exists\n";
    sync_excel_export();
    echo "Excel synced\n";
    exit;
}

$nomor = next_nomor(setting('tahun_ajaran'));
db()->prepare(
    'INSERT INTO registrations (nomor,tahun_ajaran,nama,nisn,nik,tempat_lahir,tanggal_lahir,jenis_kelamin,agama,alamat,no_hp,email,asal_sekolah,jurusan,nama_ayah,pekerjaan_ayah,nama_ibu,pekerjaan_ibu,no_hp_ortu,status,created_at,updated_at)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
)->execute([
    $nomor,
    setting('tahun_ajaran'),
    'Ahmad Fauzi',
    '1234567890',
    '1234567890123456',
    'Stabat',
    '2009-05-12',
    'L',
    'Islam',
    'Jl. Pendidikan No. 1',
    '081234567890',
    'ahmad@example.com',
    'MTsN Stabat',
    jurusan_list()[0],
    'Budi',
    'Petani',
    'Siti',
    'Ibu rumah tangga',
    '081298765432',
    'menunggu',
    $now,
    $now,
]);
$id = (int) db()->lastInsertId();
$st = db()->prepare('SELECT * FROM registrations WHERE id = ?');
$st->execute([$id]);
$reg = $st->fetch();
dispatch_registration_notifications($reg);
sync_excel_export();
echo $nomor . PHP_EOL;
echo (is_file((string) config('excel_path')) ? 'xlsx ok ' : 'xlsx fail ') . filesize((string) config('excel_path')) . PHP_EOL;
