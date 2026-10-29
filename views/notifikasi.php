<?php
$rows = db()->query('SELECT * FROM notifications ORDER BY id DESC LIMIT 80')->fetchAll();
?>
<section class="panel">
    <h2>Riwayat notifikasi</h2>
    <p class="muted">Termasuk notifikasi sistem setelah daftar dan percobaan kirim WhatsApp (API Fonnte atau antrean tautan wa.me).</p>
    <table>
        <thead>
            <tr><th>Waktu</th><th>Saluran</th><th>Tujuan</th><th>Judul</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $n): ?>
            <tr>
                <td><?= e($n['created_at']) ?></td>
                <td><?= e($n['channel']) ?></td>
                <td><?= e($n['target']) ?></td>
                <td>
                    <?= e($n['title']) ?>
                    <div class="hint"><?= e(mb_strimwidth($n['message'], 0, 120, '…')) ?></div>
                </td>
                <td><span class="badge <?= $n['status'] === 'sent' ? 'ok' : 'wait' ?>"><?= e($n['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
            <tr><td colspan="5" class="muted">Belum ada notifikasi.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
