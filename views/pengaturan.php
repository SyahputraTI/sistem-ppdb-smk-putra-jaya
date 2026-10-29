<div class="grid-2">
    <form method="post" class="panel">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_settings">
        <h2>Sekolah &amp; PPDB</h2>
        <label>Nama sekolah
            <input name="school_name" required value="<?= e(setting('school_name')) ?>">
        </label>
        <label>Alamat
            <input name="school_address" value="<?= e(setting('school_address')) ?>">
        </label>
        <label>Telepon
            <input name="school_phone" value="<?= e(setting('school_phone')) ?>">
        </label>
        <label>Email
            <input name="school_email" value="<?= e(setting('school_email')) ?>">
        </label>
        <label>Tahun ajaran
            <input name="tahun_ajaran" value="<?= e(setting('tahun_ajaran')) ?>">
        </label>
        <label>Informasi beranda
            <textarea name="info_singkat" rows="4"><?= e(setting('info_singkat')) ?></textarea>
        </label>
        <label class="check">
            <input type="checkbox" name="pendaftaran_buka" <?= setting('pendaftaran_buka') === '1' ? 'checked' : '' ?>>
            Pendaftaran dibuka
        </label>
        <h2>WhatsApp</h2>
        <p class="hint">Gunakan token <a href="https://fonnte.com" target="_blank" rel="noopener">Fonnte</a>. Jika token kosong, sistem tetap mencatat notifikasi dan menyediakan tautan wa.me.</p>
        <label>Nomor WA panitia
            <input name="wa_admin" value="<?= e(setting('wa_admin')) ?>">
        </label>
        <label>Token API Fonnte
            <input name="wa_token" value="<?= e(setting('wa_token')) ?>" autocomplete="off">
        </label>
        <input type="hidden" name="wa_provider" value="fonnte">
        <label class="check">
            <input type="checkbox" name="wa_enabled" <?= setting('wa_enabled') === '1' ? 'checked' : '' ?>>
            Aktifkan pengiriman otomatis WhatsApp
        </label>
        <button class="btn primary" type="submit">Simpan pengaturan</button>
    </form>
    <form method="post" class="panel">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">
        <h2>Sandi admin</h2>
        <label>Sandi lama
            <input type="password" name="old_password" required>
        </label>
        <label>Sandi baru
            <input type="password" name="new_password" required minlength="6">
        </label>
        <button class="btn primary" type="submit">Ubah sandi</button>
        <p class="hint">File Excel otomatis: <code>data/rekap-ppdb.xlsx</code></p>
    </form>
</div>
