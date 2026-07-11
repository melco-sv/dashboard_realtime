# ============================================================
# EKSPOR DATABASE — Dashboard Realtime
# Membuat file backup .sql lengkap (struktur + data) ke folder
# database-backup\ dengan nama berstempel waktu.
# Konfigurasi dibaca otomatis dari file .env proyek.
# ============================================================

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot

# --- Baca konfigurasi DB dari .env ---
$envFile = Join-Path $root '.env'
if (-not (Test-Path $envFile)) { Write-Host "GAGAL: file .env tidak ditemukan di $root" -ForegroundColor Red; exit 1 }
$envMap = @{}
foreach ($line in Get-Content $envFile) {
    if ($line -match '^\s*(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD)\s*=\s*(.*)$') {
        $envMap[$Matches[1]] = $Matches[2].Trim().Trim('"').Trim("'")
    }
}
$dbHost = if ($envMap['DB_HOST'])     { $envMap['DB_HOST'] }     else { 'localhost' }
$dbPort = if ($envMap['DB_PORT'])     { $envMap['DB_PORT'] }     else { '3306' }
$dbName = $envMap['DB_DATABASE']
$dbUser = if ($envMap['DB_USERNAME']) { $envMap['DB_USERNAME'] } else { 'root' }
$dbPass = $envMap['DB_PASSWORD']
if (-not $dbName) { Write-Host "GAGAL: DB_DATABASE tidak ditemukan di .env" -ForegroundColor Red; exit 1 }

# --- Cari mysqldump (PATH -> Laragon -> XAMPP) ---
$mysqldump = $null
$cmd = Get-Command mysqldump -ErrorAction SilentlyContinue
if ($cmd) { $mysqldump = $cmd.Source }
if (-not $mysqldump) {
    $cand = Get-ChildItem 'C:\laragon\bin\mysql\*\bin\mysqldump.exe' -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($cand) { $mysqldump = $cand.FullName }
}
if (-not $mysqldump -and (Test-Path 'C:\xampp\mysql\bin\mysqldump.exe')) { $mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe' }
if (-not $mysqldump) { Write-Host "GAGAL: mysqldump.exe tidak ditemukan. Pastikan Laragon/XAMPP/MySQL terpasang." -ForegroundColor Red; exit 1 }

# --- Siapkan folder & nama file output ---
$backupDir = Join-Path $root 'database-backup'
if (-not (Test-Path $backupDir)) { New-Item -ItemType Directory -Path $backupDir | Out-Null }
$stamp   = Get-Date -Format 'yyyy-MM-dd_HHmm'
$outFile = Join-Path $backupDir "${dbName}_${stamp}.sql"

Write-Host ""
Write-Host "=== EKSPOR DATABASE ===" -ForegroundColor Cyan
Write-Host "Database : $dbName  (host $dbHost`:$dbPort, user $dbUser)"
Write-Host "Output   : $outFile"
Write-Host "Sedang mengekspor, harap tunggu..." -ForegroundColor Yellow

# --- Jalankan mysqldump ---
# --result-file menghindari kerusakan encoding (jangan pakai redirect > di PowerShell)
$args = @(
    "--host=$dbHost", "--port=$dbPort", "--user=$dbUser",
    '--single-transaction',           # konsisten tanpa mengunci tabel
    '--routines', '--triggers', '--events',
    '--add-drop-table',               # impor menimpa tabel lama dengan bersih
    '--default-character-set=utf8mb4',
    '--databases', $dbName,           # sertakan CREATE DATABASE + USE
    "--result-file=$outFile"
)
if ($dbPass) { $args = @("--password=$dbPass") + $args }

& $mysqldump @args
if ($LASTEXITCODE -ne 0) {
    Write-Host "GAGAL: mysqldump keluar dengan kode $LASTEXITCODE. Pastikan MySQL sedang berjalan." -ForegroundColor Red
    if (Test-Path $outFile) { Remove-Item $outFile -Force }
    exit 1
}

$sizeMB = [math]::Round((Get-Item $outFile).Length / 1MB, 1)
Write-Host ""
Write-Host "SUKSES! Backup dibuat ($sizeMB MB):" -ForegroundColor Green
Write-Host "  $outFile"
Write-Host ""
Write-Host "Langkah berikutnya: salin file ini ke device lain (flashdisk /"
Write-Host "Google Drive), lalu jalankan db-import.bat di device tersebut."
