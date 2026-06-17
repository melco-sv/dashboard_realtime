<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ApprovalNotif extends Component
{
    public function render()
    {
        // === Jumlah dokumen HPK yang masih menunggu approval (pending) ===
        // Pending = status NULL / '' / selain 'Approve' & 'Reject'. Pakai DB::table
        // (bukan model) supaya CabangScope tidak membatasi — admin pusat lihat semua cabang.
        $pendingGabah = DB::table('mas_hpkk_gabah')
            ->where(fn ($q) => $q->whereNull('status_data')
                ->orWhereNotIn('status_data', ['Approve', 'Reject']))
            ->count();

        $pendingBeras = DB::table('mas_hpkk_beras')
            ->where(fn ($q) => $q->whereNull('status')
                ->orWhereNotIn('status', ['Approve', 'Reject']))
            ->count();

        $count = $pendingGabah + $pendingBeras;

        // === 5 perintah terakhir (hanya dihitung saat badge tampil, hemat query) ===
        // Gabungan: aksi Approve/Reject (activity_log, dengan kode HPK/LHPK) + BAST
        // (ref_bast_status, dengan nomor surat). Diurutkan terbaru, ambil 5.
        $recent = collect();

        if ($count > 0) {
            $logs = DB::table('activity_log as a')
                ->leftJoin('mas_user as u', 'a.causer_id', '=', 'u.id_user')
                ->whereIn('a.description', ['Approve GKP', 'Approve HGL', 'Reject GKP', 'Reject HGL'])
                ->orderByDesc('a.created_at')
                ->limit(5)
                ->get(['a.description', 'a.properties', 'a.created_at', 'u.nama as actor']);

            foreach ($logs as $log) {
                $props    = json_decode($log->properties ?? '{}', true) ?: [];
                $isReject = str_starts_with($log->description, 'Reject');
                $isGabah  = str_ends_with($log->description, 'GKP');
                $recent->push([
                    'label'  => $log->description,
                    'code'   => $props['no_lhpk'] ?? $props['no_hpk'] ?? '—',
                    'cabang' => $props['cabang'] ?? null,
                    'actor'  => $log->actor,
                    'time'   => $log->created_at,
                    'icon'   => $isReject ? 'fa-circle-xmark' : 'fa-circle-check',
                    'color'  => $isReject ? 'text-red-400' : 'text-green-400',
                    'url'    => $isGabah ? route('verifikasi.gabah') : route('verifikasi.beras'),
                ]);
            }

            $basts = DB::table('ref_bast_status as b')
                ->leftJoin('ref_cabang as r', 'b.code_cabang', '=', 'r.code_cabang')
                ->whereNotNull('b.nomor_surat')
                ->orderByDesc('b.created_at')
                ->limit(5)
                ->get(['b.nomor_surat', 'b.jenis', 'b.created_at', 'r.name_cabang as cabang']);

            foreach ($basts as $bast) {
                $recent->push([
                    'label'  => 'BAST ' . $bast->jenis,
                    'code'   => $bast->nomor_surat,
                    'cabang' => $bast->cabang,
                    'actor'  => null,
                    'time'   => $bast->created_at,
                    'icon'   => 'fa-file-invoice',
                    'color'  => 'text-blue-400',
                    'url'    => route('status.bayar.bast'),
                ]);
            }

            $recent = $recent
                ->sortByDesc(fn ($item) => $item['time'])
                ->take(5)
                ->values();
        }

        return view('livewire.approval-notif', [
            'count'  => $count,
            'recent' => $recent,
        ]);
    }
}
