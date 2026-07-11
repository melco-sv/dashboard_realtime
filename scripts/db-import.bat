@echo off
REM Impor database dari backup (klik dua kali = pakai backup terbaru,
REM atau seret file .sql ke atas file ini)
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0db-import.ps1" -File "%~1"
echo.
pause
