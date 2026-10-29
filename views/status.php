<?php $result = $_SESSION['status_result'] ?? null; unset($_SESSION['status_result']); ?>
<div class="grid-2">
    <form method="post" class="panel">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="cek_status">
        <h2>Cek status pendaftaran</h2>
        <p class="muted">Masukkan nomor pendaftaran dan NISN yang digunakan saat mendaftar.</p>
        <label>Nomor pendaftaran
            <input name="nomor" required placeholder="PJ20260001" value="<?= e((string) ($_POST['nomor'] ?? '')) ?>">
        </label>
        <label>NISN
            <input name="nisn" required inputmode="numeric" maxlength="10">
        </label>
        <button class="btn primary" type="submit">Lihat status</button>
    </form>
    <div class="panel">
        <?php if ($result): ?>
            <p class="eyebrow">Hasil</p>
            <h2><?= e($result['nama']) ?></h2>
            <p><span class="badge <?= e(status_badge_class($result['status'])) ?>"><?= e(status_label($result['status'])) ?></span></p>
            <p>Nomor: <strong><?= e($result['nomor']) ?></strong><br>
            Jurusan: <?= e($result['jurusan']) ?><br>
            Didaftarkan: <?= e(format_date_id($result['created_at'])) ?></p>
            <?php if (!empty($result['catatan_admin'])): ?>
                <p>Catatan panitia: <?= e($result['catatan_admin']) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <h2>Belum ada hasil</h2>
            <p class="muted">Isi formulir di samping. Jika berkas kurang, panitia menuliskan catatan di sini.</p>
        <?php endif; ?>
    </div>
</div>
