@echo off
REM Ekspor database ke folder database-backup\ (klik dua kali untuk menjalankan)
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0db-export.ps1"
echo.
pause
