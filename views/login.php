<div class="login-wrap">
    <aside class="login-aside">
        <blockquote>Panel panitia PPDB <?= e($schoolName) ?> untuk validasi berkas, rekap, dan notifikasi WhatsApp.</blockquote>
    </aside>
    <form class="login-card" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="login">
        <p class="eyebrow">Admin</p>
        <h1>Masuk</h1>
        <?php if (!empty($error)): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
        <label>Username
            <input name="username" required autocomplete="username">
        </label>
        <label>Sandi
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button class="btn primary" type="submit">Masuk</button>
        <a class="btn ghost" href="?page=beranda">Ke situs pendaftaran</a>
        <p class="hint">Awal: admin / admin123 — ganti segera di Pengaturan.</p>
    </form>
</div>
