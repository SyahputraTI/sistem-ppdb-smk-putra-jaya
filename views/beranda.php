<?php $open = setting('pendaftaran_buka', '1') === '1'; ?>
<section class="hero">
    <div>
        <h2 class="eyebrow">PPDB</h2>
        <h1>Pendaftaran daring <?= e($schoolName) ?></h1>
        <p><?= e(setting('info_singkat')) ?></p>
        <div class="actions">
            <?php if ($open): ?>
                <a class="btn primary" href="?page=daftar">Isi formulir pendaftaran</a>
            <?php else: ?>
                <span class="btn">Pendaftaran ditutup</span>
            <?php endif; ?>
            <a class="btn" href="?page=status">Cek status pendaftaran</a>
        </div>
    </div>
    <aside class="panel">
        <h2>Alur singkat</h2>
        <ol class="steps">
            <li>Isi data diri, orang tua, dan jurusan.</li>
            <li>Unggah berkas (foto, KK, akta, ijazah/SKL, rapor, KTP ortu).</li>
            <li>Simpan nomor pendaftaran. Notifikasi muncul di layar dan WhatsApp.</li>
            <li>Panitia memvalidasi. Cek status kapan saja.</li>
        </ol>
    </aside>
</section>
<section class="grid-3">
    <article class="panel">
        <h2>Validasi data</h2>
        <p class="muted">NISN 10 digit, NIK 16 digit, usia, nomor WhatsApp, dan berkas wajib diperiksa sebelum data masuk database.</p>
    </article>
    <article class="panel">
        <h2>Notifikasi WhatsApp</h2>
        <p class="muted">Setelah daftar, pesan konfirmasi dikirim ke nomor siswa. Panitia juga mendapat pemberitahuan pendaftar baru.</p>
    </article>
    <article class="panel">
        <h2>Rekap Excel</h2>
        <p class="muted">Setiap pendaftaran otomatis diperbarui ke file Excel rekap panitia, siap diunduh dari dashboard admin.</p>
    </article>
</section>
