<?php
$total = (int) db()->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
$byStatus = [];
foreach (status_list() as $key => $_label) {
    $st = db()->prepare('SELECT COUNT(*) FROM registrations WHERE status = ?');
    $st->execute([$key]);
    $byStatus[$key] = (int) $st->fetchColumn();
}
$byJurusan = db()->query('SELECT jurusan, COUNT(*) AS n FROM registrations GROUP BY jurusan ORDER BY n DESC')->fetchAll();
$latest = db()->query('SELECT * FROM registrations ORDER BY id DESC LIMIT 8')->fetchAll();
$maxJ = 1;
foreach ($byJurusan as $j) {
    $maxJ = max($maxJ, (int) $j['n']);
}
?>
<div class="grid-4">
    <div class="stat"><span>Total pendaftar</span><strong><?= $total ?></strong><small>Tahun ajaran <?= e(setting('tahun_ajaran')) ?></small></div>
    <div class="stat"><span>Menunggu</span><strong><?= $byStatus['menunggu'] ?></strong><small>Perlu divalidasi</small></div>
    <div class="stat"><span>Diterima</span><strong><?= $byStatus['diterima'] ?></strong><small>Sudah lulus seleksi</small></div>
    <div class="stat"><span>Berkas kurang</span><strong><?= $byStatus['berkas_kurang'] ?></strong><small>Perlu dilengkapi</small></div>
</div>
<div class="grid-2">
    <section class="panel">
        <div class="panel-head">
            <h2>Rekap per status</h2>
            <a class="btn" href="?page=excel">Unduh Excel</a>
        </div>
        <?php foreach (status_list() as $key => $label): ?>
            <?php $n = $byStatus[$key]; $pct = $total ? round($n / $total * 100) : 0; ?>
            <div class="rekap-row">
                <span><?= e($label) ?></span>
                <strong><?= $n ?></strong>
            </div>
            <div class="bar"><i style="width:<?= $pct ?>%"></i></div>
        <?php endforeach; ?>
    </section>
    <section class="panel">
        <h2>Rekap jurusan</h2>
        <?php if (!$byJurusan): ?>
            <p class="muted">Belum ada pendaftar.</p>
        <?php endif; ?>
        <?php foreach ($byJurusan as $j): ?>
            <?php $pct = round(((int) $j['n']) / $maxJ * 100); ?>
            <div class="rekap-row">
                <span><?= e($j['jurusan']) ?></span>
                <strong><?= (int) $j['n'] ?></strong>
            </div>
            <div class="bar"><i style="width:<?= $pct ?>%"></i></div>
        <?php endforeach; ?>
    </section>
</div>
<section class="panel">
    <div class="panel-head">
        <h2>Pendaftar terbaru</h2>
        <a href="?page=pendaftar">Lihat semua</a>
    </div>
    <table>
        <thead><tr><th>Nomor</th><th>Nama</th><th>Jurusan</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($latest as $r): ?>
            <tr>
                <td><?= e($r['nomor']) ?></td>
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['jurusan']) ?></td>
                <td><span class="badge <?= e(status_badge_class($r['status'])) ?>"><?= e(status_label($r['status'])) ?></span></td>
                <td><a href="?page=detail&id=<?= (int) $r['id'] ?>">Validasi</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$latest): ?>
            <tr><td colspan="5" class="muted">Belum ada data.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
