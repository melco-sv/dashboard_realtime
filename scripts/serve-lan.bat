@echo off
REM Jalankan aplikasi agar bisa diakses device lain di WiFi yang sama
REM (klik dua kali untuk menjalankan; tutup jendela untuk mematikan)
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0serve-lan.ps1"
pause
