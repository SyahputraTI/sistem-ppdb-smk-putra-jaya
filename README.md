# Sistem Pendaftaran SMKS-PP Putra Jaya Stabat

Aplikasi PPDB berbasis web (PHP + MySQL) untuk pendaftaran siswa baru secara daring.

## Fitur

- Formulir pendaftaran online (data siswa, orang tua, jurusan)
- Unggah berkas (pas foto, KK, akta, ijazah/SKL, rapor, KTP orang tua)
- Validasi data (NISN, NIK, usia, nomor HP, jenis berkas, duplikat)
- Cek status pendaftaran dengan nomor + NISN
- Login admin, dashboard rekap per status dan jurusan
- Validasi berkas dan ubah status
- Notifikasi di layar setelah daftar
- Notifikasi WhatsApp (API Fonnte, atau tautan wa.me jika token belum diisi)
- Rekap Excel otomatis di `data/rekap-ppdb.xlsx` (juga bisa diunduh dari admin)

## Persiapan

1. Jalankan **MySQL** di XAMPP.
2. Periksa `config/app.php` (bawaan XAMPP: user `root`, sandi kosong).
3. Database `ppdb_smkspp` dibuat otomatis saat aplikasi pertama kali dibuka.

## Menjalankan

Double-klik `start.bat`, atau:

```bat
C:\xampp\php\php.exe -S localhost:8080 router.php
```

Buka [http://localhost:8080](http://localhost:8080)

Admin awal:

- Username: `admin`
- Sandi: `admin123`

## WhatsApp

1. Daftar di [Fonnte](https://fonnte.com) dan hubungkan perangkat WhatsApp.
2. Di **Pengaturan** admin, isi token, nomor panitia, lalu centang *Aktifkan pengiriman otomatis WhatsApp*.
3. Tanpa token, pendaftar tetap mendapat halaman konfirmasi dan tombol buka WhatsApp (`wa.me`).

Setiap pendaftaran baru juga memperbarui file Excel rekap untuk panitia.
