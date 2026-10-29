<?php
$id = (int) ($_GET['id'] ?? 0);
$st = db()->prepare('SELECT * FROM registrations WHERE id = ?');
$st->execute([$id]);
$r = $st->fetch();
if (!$r) {
    echo '<div class="panel">Data tidak ditemukan. <a href="?page=pendaftar">Kembali</a></div>';
    return;
}
$docs = db()->prepare('SELECT * FROM documents WHERE registration_id = ?');
$docs->execute([$id]);
$docs = $docs->fetchAll();
$byType = [];
foreach ($docs as $d) {
    $byType[$d['doc_type']] = $d;
}
$waLink = wa_me_link($r['no_hp'], wa_message_status($r));
?>
<div class="split">
    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow"><?= e($r['nomor']) ?></p>
                <h2><?= e($r['nama']) ?></h2>
            </div>
            <span class="badge <?= e(status_badge_class($r['status'])) ?>"><?= e(status_label($r['status'])) ?></span>
        </div>
        <table>
            <tr><th>NISN</th><td><?= e($r['nisn']) ?></td></tr>
            <tr><th>NIK</th><td><?= e($r['nik']) ?></td></tr>
            <tr><th>TTL</th><td><?= e($r['tempat_lahir']) ?>, <?= e(format_date_id($r['tanggal_lahir'])) ?></td></tr>
            <tr><th>JK / Agama</th><td><?= e($r['jenis_kelamin']) ?> / <?= e($r['agama']) ?></td></tr>
            <tr><th>Alamat</th><td><?= e($r['alamat']) ?></td></tr>
            <tr><th>WA siswa</th><td><?= e($r['no_hp']) ?></td></tr>
            <tr><th>Email</th><td><?= e((string) $r['email']) ?></td></tr>
            <tr><th>Asal sekolah</th><td><?= e($r['asal_sekolah']) ?></td></tr>
            <tr><th>Jurusan</th><td><?= e($r['jurusan']) ?></td></tr>
            <tr><th>Ayah</th><td><?= e($r['nama_ayah']) ?> (<?= e($r['pekerjaan_ayah']) ?>)</td></tr>
            <tr><th>Ibu</th><td><?= e($r['nama_ibu']) ?> (<?= e($r['pekerjaan_ibu']) ?>)</td></tr>
            <tr><th>HP ortu</th><td><?= e($r['no_hp_ortu']) ?></td></tr>
        </table>
        <h3>Berkas</h3>
        <ul class="file-list">
            <?php foreach (document_types() as $type => $label): ?>
                <li>
                    <?= e($label) ?> —
                    <?php if (!empty($byType[$type])): ?>
                        <a href="?page=berkas&id=<?= (int) $byType[$type]['id'] ?>" target="_blank">Lihat</a>
                    <?php else: ?>
                        <span class="badge no">Belum ada</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <aside class="panel sticky">
        <h2>Validasi data</h2>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <label>Status
                <select name="status">
                    <?php foreach (status_list() as $k => $label): ?>
                        <option value="<?= e($k) ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Catatan panitia
                <textarea name="catatan_admin" rows="4"><?= e((string) $r['catatan_admin']) ?></textarea>
            </label>
            <button class="btn primary" type="submit">Simpan &amp; kirim WhatsApp</button>
        </form>
        <form method="post" class="mt">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="kirim_wa">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="btn" type="submit">Kirim ulang notifikasi WA</button>
        </form>
        <a class="btn ghost" href="<?= e($waLink) ?>" target="_blank" rel="noopener">Buka WhatsApp manual</a>
    </aside>
</div>
