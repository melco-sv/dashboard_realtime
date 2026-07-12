# ============================================================
# IMPOR DATABASE - Dashboard Realtime
# Mengimpor file backup .sql (hasil db-export) ke MySQL/MariaDB lokal.
# PERINGATAN: seluruh isi database lokal akan DITIMPA oleh isi backup.
#
# Cara pakai:
#   - Jalankan db-import.bat  -> otomatis memakai backup TERBARU
#     di folder database-backup\
#   - Atau seret (drag & drop) file .sql ke atas db-import.bat
#
# Kompatibilitas: bila server tujuan MariaDB (bawaan XAMPP), collation
# khusus MySQL 8 (utf8mb4_0900_*) otomatis dikonversi agar impor sukses.
# ============================================================

param(
    [string]$File,
    [switch]$Yes   # lewati konfirmasi (untuk otomasi/pengujian)
)

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
    if ($line -match '^\s*(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD|MYSQL_BIN_DIR)\s*=\s*(.*)$') {
        $envMap[$Matches[1]] = $Matches[2].Trim().Trim('"').Trim("'")
    }
}
$dbHost = if ($envMap['DB_HOST'])     { $envMap['DB_HOST'] }     else { 'localhost' }
$dbPort = if ($envMap['DB_PORT'])     { $envMap['DB_PORT'] }     else { '3306' }
$dbName = $envMap['DB_DATABASE']
$dbUser = if ($envMap['DB_USERNAME']) { $envMap['DB_USERNAME'] } else { 'root' }
$dbPass = $envMap['DB_PASSWORD']

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

$binDir = Find-MySqlBinDir $envMap $dbHost $dbPort $dbUser $dbPass
if (-not $binDir) {
    Write-Host "GAGAL: tidak menemukan client MySQL yang bisa terhubung." -ForegroundColor Red
    Write-Host "Bila lokasi MySQL/XAMPP Anda tidak standar, tambahkan ke .env:"
    Write-Host "  MYSQL_BIN_DIR=D:\xampp\mysql\bin" -ForegroundColor Cyan
    exit 1
}
$mysql = Join-Path $binDir 'mysql.exe'
Write-Host "Memakai: $mysql" -ForegroundColor DarkGray

# --- Deteksi server tujuan (MySQL vs MariaDB) ---
$passPart = ''; if ($dbPass) { $passPart = " --password=$dbPass" }
$serverVer = cmd /c "`"$mysql`" --host=$dbHost --port=$dbPort --user=$dbUser$passPart -N -e `"SELECT VERSION();`" 2>nul"
$isMariaDb = "$serverVer" -match 'MariaDB'

# --- Konfirmasi (aksi menimpa!) ---
$info   = Get-Item $File
$sizeMB = [math]::Round($info.Length / 1MB, 1)
Write-Host ""
Write-Host "=== IMPOR DATABASE ===" -ForegroundColor Cyan
Write-Host "File backup : $($info.Name)  ($sizeMB MB, dibuat $($info.LastWriteTime))"
Write-Host "Target      : database '$dbName' di $dbHost`:$dbPort  (server: $serverVer)"
Write-Host ""
Write-Host "PERINGATAN: seluruh data '$dbName' di device INI akan DITIMPA" -ForegroundColor Yellow
Write-Host "dengan isi file backup. Data lokal yang belum diekspor akan hilang." -ForegroundColor Yellow
if (-not $Yes) {
    $jawab = Read-Host "Ketik YA untuk melanjutkan"
    if ($jawab -ne 'YA') { Write-Host "Dibatalkan. Tidak ada yang diubah."; exit 0 }
}

# --- Kompatibilitas MariaDB: konversi collation MySQL 8 (utf8mb4_0900_*) ---
$importFile = $File
$tmpCompat  = $null
if ($isMariaDb -and (Select-String -Path $File -Pattern 'utf8mb4_0900_' -Quiet)) {
    Write-Host "Server tujuan MariaDB terdeteksi - mengonversi collation MySQL 8..." -ForegroundColor Yellow
    $tmpCompat = Join-Path $env:TEMP ("dbimport_compat_" + [IO.Path]::GetFileName($File))
    (Get-Content $File -Raw) `
        -replace 'utf8mb4_0900_bin', 'utf8mb4_bin' `
        -replace 'utf8mb4_0900_[a-z_]+', 'utf8mb4_unicode_ci' |
        Set-Content $tmpCompat -Encoding UTF8 -NoNewline
    $importFile = $tmpCompat
}

Write-Host "Sedang mengimpor, harap tunggu..." -ForegroundColor Yellow

# --- Jalankan impor (redirect via cmd agar aliran byte utuh) ---
$passPart = ''
if ($dbPass) { $passPart = " --password=$dbPass" }
$cmdLine = "`"$mysql`" --host=$dbHost --port=$dbPort --user=$dbUser$passPart --default-character-set=utf8mb4 < `"$importFile`""
cmd /c $cmdLine
$importExit = $LASTEXITCODE
if ($tmpCompat -and (Test-Path $tmpCompat)) { Remove-Item $tmpCompat -Force }
if ($importExit -ne 0) {
    Write-Host "GAGAL: impor berhenti dengan kode $importExit. Pastikan server berjalan & file backup utuh." -ForegroundColor Red
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
