<?php
$form = $_SESSION['form'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form'], $_SESSION['form_errors']);
$open = setting('pendaftaran_buka', '1') === '1';
$val = function (string $key) use ($form): string {
    return e((string) ($form[$key] ?? ''));
};
$err = function (string $key) use ($errors): void {
    if (!empty($errors[$key])) {
        echo '<small class="field-error">' . e($errors[$key]) . '</small>';
    }
};
?>
<div class="panel">
    <div class="panel-head">
        <div>
            <h2>Formulir pendaftaran online</h2>
            <p class="muted">Lengkapi semua kolom bertanda wajib. Berkas JPG, PNG, atau PDF maksimal <?= e((string) config('upload_max_mb')) ?> MB.</p>
        </div>
        <span class="badge <?= $open ? 'ok' : 'no' ?>"><?= $open ? 'Pendaftaran dibuka' : 'Pendaftaran ditutup' ?></span>
    </div>
    <?php if (!$open): ?>
        <p>Pendaftaran sedang ditutup. Silakan cek status jika Anda sudah mendaftar.</p>
    <?php else: ?>
    <form method="post" enctype="multipart/form-data" class="stack" id="form-daftar" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="daftar">

        <h3>Data calon siswa</h3>
        <div class="grid-2 tight">
            <label>Nama lengkap *
                <input name="nama" required minlength="3" value="<?= $val('nama') ?>">
                <?php $err('nama'); ?>
            </label>
            <label>NISN (10 digit) *
                <input name="nisn" required inputmode="numeric" maxlength="10" value="<?= $val('nisn') ?>">
                <?php $err('nisn'); ?>
            </label>
            <label>NIK (16 digit) *
                <input name="nik" required inputmode="numeric" maxlength="16" value="<?= $val('nik') ?>">
                <?php $err('nik'); ?>
            </label>
            <label>Tempat lahir *
                <input name="tempat_lahir" required value="<?= $val('tempat_lahir') ?>">
                <?php $err('tempat_lahir'); ?>
            </label>
            <label>Tanggal lahir *
                <input type="date" name="tanggal_lahir" required value="<?= $val('tanggal_lahir') ?>">
                <?php $err('tanggal_lahir'); ?>
            </label>
            <label>Jenis kelamin *
                <select name="jenis_kelamin" required>
                    <option value="">Pilih</option>
                    <option value="L" <?= ($form['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= ($form['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
                <?php $err('jenis_kelamin'); ?>
            </label>
            <label>Agama *
                <select name="agama" required>
                    <option value="">Pilih</option>
                    <?php foreach (agama_list() as $ag): ?>
                        <option <?= ($form['agama'] ?? '') === $ag ? 'selected' : '' ?>><?= e($ag) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php $err('agama'); ?>
            </label>
            <label>Nomor WhatsApp siswa *
                <input name="no_hp" required placeholder="08xxxxxxxxxx" value="<?= $val('no_hp') ?>">
                <?php $err('no_hp'); ?>
            </label>
            <label>Email
                <input type="email" name="email" value="<?= $val('email') ?>">
                <?php $err('email'); ?>
            </label>
            <label>Asal sekolah *
                <input name="asal_sekolah" required value="<?= $val('asal_sekolah') ?>">
                <?php $err('asal_sekolah'); ?>
            </label>
            <label>Pilihan jurusan *
                <select name="jurusan" required>
                    <option value="">Pilih jurusan</option>
                    <?php foreach (jurusan_list() as $j): ?>
                        <option <?= ($form['jurusan'] ?? '') === $j ? 'selected' : '' ?>><?= e($j) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php $err('jurusan'); ?>
            </label>
        </div>
        <label>Alamat lengkap *
            <textarea name="alamat" rows="3" required><?= $val('alamat') ?></textarea>
            <?php $err('alamat'); ?>
        </label>

        <h3>Data orang tua / wali</h3>
        <div class="grid-2 tight">
            <label>Nama ayah *
                <input name="nama_ayah" required value="<?= $val('nama_ayah') ?>">
                <?php $err('nama_ayah'); ?>
            </label>
            <label>Pekerjaan ayah *
                <input name="pekerjaan_ayah" required value="<?= $val('pekerjaan_ayah') ?>">
                <?php $err('pekerjaan_ayah'); ?>
            </label>
            <label>Nama ibu *
                <input name="nama_ibu" required value="<?= $val('nama_ibu') ?>">
                <?php $err('nama_ibu'); ?>
            </label>
            <label>Pekerjaan ibu *
                <input name="pekerjaan_ibu" required value="<?= $val('pekerjaan_ibu') ?>">
                <?php $err('pekerjaan_ibu'); ?>
            </label>
            <label>Nomor HP orang tua *
                <input name="no_hp_ortu" required placeholder="08xxxxxxxxxx" value="<?= $val('no_hp_ortu') ?>">
                <?php $err('no_hp_ortu'); ?>
            </label>
        </div>

        <h3>Unggah berkas</h3>
        <div class="grid-2 tight">
            <?php foreach (document_types() as $type => $label): ?>
                <label><?= e($label) ?> *
                    <input type="file" name="<?= e($type) ?>" accept=".jpg,.jpeg,.png,.pdf" required>
                    <?php $err($type); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="actions">
            <button class="btn primary" type="submit">Kirim pendaftaran</button>
            <p class="hint">Setelah berhasil, nomor pendaftaran tampil di layar dan notifikasi WhatsApp disiapkan.</p>
        </div>
    </form>
    <?php endif; ?>
</div>
