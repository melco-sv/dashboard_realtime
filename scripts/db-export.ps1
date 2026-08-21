# ============================================================
# EKSPOR DATABASE - Dashboard Realtime
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
    if ($line -match '^\s*(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD|MYSQL_BIN_DIR)\s*=\s*(.*)$') {
        $envMap[$Matches[1]] = $Matches[2].Trim().Trim('"').Trim("'")
    }
}
$dbHost = if ($envMap['DB_HOST'])     { $envMap['DB_HOST'] }     else { 'localhost' }
$dbPort = if ($envMap['DB_PORT'])     { $envMap['DB_PORT'] }     else { '3306' }
$dbName = $envMap['DB_DATABASE']
$dbUser = if ($envMap['DB_USERNAME']) { $envMap['DB_USERNAME'] } else { 'root' }
$dbPass = $envMap['DB_PASSWORD']
if (-not $dbName) { Write-Host "GAGAL: DB_DATABASE tidak ditemukan di .env" -ForegroundColor Red; exit 1 }

# --- Cari folder bin MySQL yang BISA TERHUBUNG ke server yang berjalan.
#     Kandidat: MYSQL_BIN_DIR (.env) -> PATH -> semua drive (XAMPP /
#     Laragon / WAMP / MySQL Server). Tiap kandidat diuji koneksi dulu,
#     karena client MariaDB (XAMPP) tidak bisa masuk ke server MySQL 8
#     dan sebaliknya bila keduanya terpasang. ---
function Find-MySqlBinDir([hashtable]$envMap, $dbHost, $dbPort, $dbUser, $dbPass) {
    $dirs = @()
    if ($envMap['MYSQL_BIN_DIR']) { $dirs += $envMap['MYSQL_BIN_DIR'] }
    $cmd = Get-Command mysql -ErrorAction SilentlyContinue
    if ($cmd) { $dirs += (Split-Path -Parent $cmd.Source) }
    foreach ($drive in (Get-PSDrive -PSProvider FileSystem | Where-Object { $_.Root -match '^[A-Z]:\\$' })) {
        $r = $drive.Root
        $dirs += (Join-Path $r 'xampp\mysql\bin')
        foreach ($glob in @('laragon\bin\mysql\*\bin', 'wamp64\bin\mysql\*\bin', 'wamp\bin\mysql\*\bin')) {
            $dirs += (Get-ChildItem (Join-Path $r $glob) -Directory -ErrorAction SilentlyContinue | ForEach-Object FullName)
        }
    }
    $dirs += (Get-ChildItem "$env:ProgramFiles\MySQL\MySQL Server*\bin" -Directory -ErrorAction SilentlyContinue | ForEach-Object FullName)

    $found = @()
    foreach ($d in ($dirs | Where-Object { $_ } | Select-Object -Unique)) {
        $exe = Join-Path $d 'mysql.exe'
        if (-not (Test-Path $exe)) { continue }
        $found += $d
        # Uji koneksi sungguhan ke server (via cmd agar stderr client yang
        # gagal konek tidak dianggap error fatal oleh PowerShell)
        $passPart = ''; if ($dbPass) { $passPart = " --password=$dbPass" }
        cmd /c "`"$exe`" --host=$dbHost --port=$dbPort --user=$dbUser$passPart -N -e `"SELECT 1;`" >nul 2>&1"
        if ($LASTEXITCODE -eq 0) { return $d }
    }
    if ($found.Count -gt 0) {
        Write-Host "Client MySQL ditemukan di:" -ForegroundColor Yellow
        $found | ForEach-Object { Write-Host "  - $_" -ForegroundColor Yellow }
        Write-Host "...tapi tidak ada yang berhasil terhubung ke $dbHost`:$dbPort." -ForegroundColor Yellow
        Write-Host "Pastikan server database sudah BERJALAN (start dari XAMPP/Laragon) dan kredensial di .env benar."
    }
    return $null
}
<<<<<<< Updated upstream

$binDir = Find-MySqlBinDir $envMap $dbHost $dbPort $dbUser $dbPass
if (-not $binDir) {
    Write-Host "GAGAL: tidak menemukan client MySQL yang bisa terhubung." -ForegroundColor Red
    Write-Host "Bila lokasi MySQL/XAMPP Anda tidak standar, tambahkan ke .env:"
    Write-Host "  MYSQL_BIN_DIR=D:\xampp\mysql\bin" -ForegroundColor Cyan
    exit 1
}
$mysqldump = Join-Path $binDir 'mysqldump.exe'
if (-not (Test-Path $mysqldump)) { Write-Host "GAGAL: mysqldump.exe tidak ada di $binDir" -ForegroundColor Red; exit 1 }
Write-Host "Memakai: $mysqldump" -ForegroundColor DarkGray
=======
if (-not $mysqldump -and (Test-Path 'D:\xampp\mysql\bin\mysqldump.exe')) { $mysqldump = 'D:\xampp\mysql\bin\mysqldump.exe' }
if (-not $mysqldump) { Write-Host "GAGAL: mysqldump.exe tidak ditemukan. Pastikan Laragon/XAMPP/MySQL terpasang." -ForegroundColor Red; exit 1 }
>>>>>>> Stashed changes

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
