<?php
$q = trim((string) ($_GET['q'] ?? ''));
$stFilter = (string) ($_GET['status'] ?? '');
$sql = 'SELECT * FROM registrations WHERE 1=1';
$params = [];
if ($q !== '') {
    $sql .= ' AND (nama LIKE ? OR nomor LIKE ? OR nisn LIKE ?)';
    $like = '%' . $q . '%';
    $params = [$like, $like, $like];
}
if ($stFilter !== '' && isset(status_list()[$stFilter])) {
    $sql .= ' AND status = ?';
    $params[] = $stFilter;
}
$sql .= ' ORDER BY id DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>
<section class="panel">
    <form class="panel-head" method="get">
        <input type="hidden" name="page" value="pendaftar">
        <div class="inline">
            <input name="q" placeholder="Cari nama, nomor, NISN" value="<?= e($q) ?>">
            <select name="status">
                <option value="">Semua status</option>
                <?php foreach (status_list() as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= $stFilter === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn primary" type="submit">Filter</button>
        </div>
        <a class="btn" href="?page=excel">Unduh Excel</a>
    </form>
    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Nama</th>
                <th>NISN</th>
                <th>Jurusan</th>
                <th>WA</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['nomor']) ?></td>
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['nisn']) ?></td>
                <td><?= e($r['jurusan']) ?></td>
                <td><?= e($r['no_hp']) ?></td>
                <td><span class="badge <?= e(status_badge_class($r['status'])) ?>"><?= e(status_label($r['status'])) ?></span></td>
                <td><a href="?page=detail&id=<?= (int) $r['id'] ?>">Validasi</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
            <tr><td colspan="7" class="muted">Tidak ada pendaftar.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
