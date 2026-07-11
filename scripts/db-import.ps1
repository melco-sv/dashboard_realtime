# ============================================================
# IMPOR DATABASE — Dashboard Realtime
# Mengimpor file backup .sql (hasil db-export) ke MySQL lokal.
# PERINGATAN: seluruh isi database lokal akan DITIMPA oleh isi backup.
#
# Cara pakai:
#   - Jalankan db-import.bat  -> otomatis memakai backup TERBARU
#     di folder database-backup\
#   - Atau seret (drag & drop) file .sql ke atas db-import.bat
# ============================================================

param([string]$File)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot

# --- Tentukan file backup ---
if (-not $File) {
    $latest = Get-ChildItem (Join-Path $root 'database-backup\*.sql') -ErrorAction SilentlyContinue |
        Sort-Object LastWriteTime -Descending | Select-Object -First 1
    if (-not $latest) {
        Write-Host "GAGAL: tidak ada file .sql di folder database-backup\." -ForegroundColor Red
        Write-Host "Salin file backup dari device lain ke folder itu dulu, atau seret file .sql ke db-import.bat."
        exit 1
    }
    $File = $latest.FullName
}
if (-not (Test-Path $File)) { Write-Host "GAGAL: file tidak ditemukan: $File" -ForegroundColor Red; exit 1 }

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

# --- Cari mysql.exe (PATH -> Laragon -> XAMPP) ---
$mysql = $null
$cmd = Get-Command mysql -ErrorAction SilentlyContinue
if ($cmd) { $mysql = $cmd.Source }
if (-not $mysql) {
    $cand = Get-ChildItem 'C:\laragon\bin\mysql\*\bin\mysql.exe' -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($cand) { $mysql = $cand.FullName }
}
if (-not $mysql -and (Test-Path 'C:\xampp\mysql\bin\mysql.exe')) { $mysql = 'C:\xampp\mysql\bin\mysql.exe' }
if (-not $mysql) { Write-Host "GAGAL: mysql.exe tidak ditemukan. Pastikan Laragon/XAMPP/MySQL terpasang." -ForegroundColor Red; exit 1 }

# --- Konfirmasi (aksi menimpa!) ---
$info   = Get-Item $File
$sizeMB = [math]::Round($info.Length / 1MB, 1)
Write-Host ""
Write-Host "=== IMPOR DATABASE ===" -ForegroundColor Cyan
Write-Host "File backup : $($info.Name)  ($sizeMB MB, dibuat $($info.LastWriteTime))"
Write-Host "Target      : database '$dbName' di $dbHost`:$dbPort"
Write-Host ""
Write-Host "PERINGATAN: seluruh data '$dbName' di device INI akan DITIMPA" -ForegroundColor Yellow
Write-Host "dengan isi file backup. Data lokal yang belum diekspor akan hilang." -ForegroundColor Yellow
$jawab = Read-Host "Ketik YA untuk melanjutkan"
if ($jawab -ne 'YA') { Write-Host "Dibatalkan. Tidak ada yang diubah."; exit 0 }

Write-Host "Sedang mengimpor, harap tunggu..." -ForegroundColor Yellow

# --- Jalankan impor (redirect via cmd agar aliran byte utuh) ---
$passPart = ''
if ($dbPass) { $passPart = " --password=$dbPass" }
$cmdLine = "`"$mysql`" --host=$dbHost --port=$dbPort --user=$dbUser$passPart --default-character-set=utf8mb4 < `"$File`""
cmd /c $cmdLine
if ($LASTEXITCODE -ne 0) {
    Write-Host "GAGAL: impor berhenti dengan kode $LASTEXITCODE. Pastikan MySQL berjalan & file backup utuh." -ForegroundColor Red
    exit 1
}

# --- Bersihkan cache Laravel agar data baru langsung terbaca segar ---
if (Test-Path (Join-Path $root 'artisan')) {
    Push-Location $root
    php artisan cache:clear | Out-Null
    Pop-Location
}

Write-Host ""
Write-Host "SUKSES! Database '$dbName' kini berisi data dari backup." -ForegroundColor Green
Write-Host "Cache aplikasi sudah dibersihkan. Silakan refresh browser."
