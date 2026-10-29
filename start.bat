@echo off
cd /d "%~dp0"
echo PPDB SMKS-PP Putra Jaya Stabat
echo http://localhost:8080
echo Admin: admin / admin123
echo Pastikan MySQL XAMPP sudah berjalan.
echo.
"C:\xampp\php\php.exe" -S localhost:8080 router.php
pause
