# ============================================================
# SERVER LAN - Dashboard Realtime
# Menjalankan aplikasi agar bisa diakses device lain di jaringan
# WiFi/LAN yang sama, lewat http://<IP-device-ini>:8000
#
# Yang dijalankan:
#   1. Queue worker (wajib untuk cetak PDF BAST) - di jendela terpisah
#   2. Server Laravel yang terikat ke semua jaringan (0.0.0.0)
#
# Catatan penting:
#   - Saat pertama kali dijalankan, Windows akan menampilkan dialog
#     "Windows Defender Firewall" -> klik ALLOW ACCESS (jaringan Private)
#     agar device lain bisa masuk.
#   - Link hanya hidup selama jendela ini terbuka & device ini menyala.
#   - Pakai hanya di WiFi terpercaya (rumah/kantor), jangan WiFi publik.
# ============================================================

param([int]$Port = 8000)

$root = Split-Path -Parent $PSScriptRoot

# --- Deteksi IP LAN (abaikan loopback & APIPA 169.x) ---
$ips = Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue |
    Where-Object { $_.InterfaceAlias -notmatch 'Loopback' -and $_.IPAddress -notmatch '^169\.' } |
    Select-Object -ExpandProperty IPAddress

Write-Host ""
Write-Host "=== SERVER LAN DASHBOARD ===" -ForegroundColor Cyan
if ($ips) {
    Write-Host ""
    Write-Host "Buka dari device lain (WiFi yang sama) lewat:" -ForegroundColor Green
    foreach ($ip in $ips) {
        Write-Host "    http://$ip`:$Port" -ForegroundColor Green
    }
    Write-Host ""
    Write-Host "Dari device ini sendiri:  http://localhost:$Port"
} else {
    Write-Host "PERINGATAN: IP LAN tidak terdeteksi - pastikan WiFi/LAN tersambung." -ForegroundColor Yellow
}
Write-Host ""
Write-Host "Jika muncul dialog Windows Firewall: klik 'Allow access'." -ForegroundColor Yellow
Write-Host "Tutup jendela ini (atau Ctrl+C) untuk mematikan server."
Write-Host ""

# --- Queue worker di jendela terpisah (untuk cetak PDF BAST) ---
Start-Process php -ArgumentList 'artisan', 'queue:work', '--tries=3' -WorkingDirectory $root
Write-Host "Queue worker dinyalakan di jendela terpisah (biarkan terbuka)." -ForegroundColor DarkGray
Write-Host ""

# --- Server Laravel, terikat ke semua jaringan ---
Set-Location $root
php artisan serve --host=0.0.0.0 --port=$Port
