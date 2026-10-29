<?php
$info = $_SESSION['sukses'] ?? null;
if (!$info) {
    echo '<div class="panel"><p>Tidak ada data konfirmasi. Silakan daftar atau cek status.</p></div>';
    return;
}
?>
<div class="panel notice-ok">
    <p class="eyebrow">Pendaftaran masuk</p>
    <h2>Terima kasih, <?= e($info['nama']) ?></h2>
    <p>Data Anda sudah tersimpan. Simpan nomor pendaftaran ini untuk cek status.</p>
    <p class="nomor-besar"><?= e($info['nomor']) ?></p>
    <?php if (!empty($info['wa_api'])): ?>
        <div class="alert ok">Notifikasi WhatsApp sudah dikirim ke nomor yang Anda daftarkan.</div>
    <?php else: ?>
        <div class="alert ok">Notifikasi layar sudah tampil. Jika WhatsApp API belum aktif, buka tautan di bawah untuk mengirim konfirmasi lewat WhatsApp.</div>
    <?php endif; ?>
    <div class="actions">
        <a class="btn primary" href="<?= e($info['wa_link']) ?>" target="_blank" rel="noopener">Buka notifikasi WhatsApp</a>
        <a class="btn" href="?page=status">Cek status</a>
        <a class="btn" href="?page=beranda">Kembali ke beranda</a>
    </div>
</div>
