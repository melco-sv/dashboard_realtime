<?php

namespace App\Livewire;

use App\Models\MasHpkkBeras;
use App\Models\MasHpkkGabah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ApprovalNotif extends Component
{
    public function render()
    {
        $user   = Auth::user();
        $mode   = null;
        $count  = 0;
        $recent = collect();

        if ($user && $user->isVerification()) {
            $mode = 'verification';
            [$count, $recent] = $this->forVerification();
        } elseif ($user && $user->isInspektor()) {
            $mode = 'inspektor';
            [$count, $recent] = $this->forInspektor();
        }

        return view('livewire.approval-notif', [
            'mode'   => $mode,
            'count'  => $count,
            'recent' => $recent,
        ]);
    }

    /**
     * ADMIN PUSAT — item yang masih perlu ditindaklanjuti (hilang saat selesai):
     * HPK belum diproses + BAST belum dibayar. Pakai DB::table agar lihat semua cabang.
     */
    private function forVerification(): array
    {
        $pendingGabah = DB::table('mas_hpkk_gabah')
            ->where(fn ($q) => $q->whereNull('status_data')->orWhereNotIn('status_data', ['Approve', 'Reject']))
            ->count();

        $pendingBeras = DB::table('mas_hpkk_beras')
            ->where(fn ($q) => $q->whereNull('status')->orWhereNotIn('status', ['Approve', 'Reject']))
            ->count();

        $pendingBast = DB::table('ref_bast_status')->where('status', 'BELUM DIBAYAR')->count();

        $count  = $pendingGabah + $pendingBeras + $pendingBast;
        $recent = collect();

        if ($count > 0) {
            $gabah = DB::table('mas_hpkk_gabah as m')
                ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
                ->where(fn ($q) => $q->whereNull('m.status_data')->orWhereNotIn('m.status_data', ['Approve', 'Reject']))
                ->orderByDesc('m.id_hpkk_gabah')->limit(6)
                ->get(['m.id_hpkk_gabah as id', 'm.nomor_hpkk_gabah as code', 'm.tanggal_pelaksanaan as time', 'r.name_cabang as cabang']);

            foreach ($gabah as $g) {
                $recent->push([
                    'label'  => 'Perlu Approve — GKP',
                    'code'   => $g->code ?: '—',
                    'cabang' => $g->cabang,
                    'note'   => null,
                    'time'   => $g->time,
                    'icon'   => 'fa-wheat-awn',
                    'color'  => 'text-yellow-400',
                    'url'    => route('view.foto.gabah', $g->id),
                ]);
            }

            $beras = DB::table('mas_hpkk_beras as m')
                ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
                ->where(fn ($q) => $q->whereNull('m.status')->orWhereNotIn('m.status', ['Approve', 'Reject']))
                ->orderByDesc('m.id_hpkk_beras')->limit(6)
                ->get(['m.id_hpkk_beras as id', 'm.nomor_hpkk_beras as code', 'm.tanggal_pemeriksaan as time', 'r.name_cabang as cabang']);

            foreach ($beras as $b) {
                $recent->push([
                    'label'  => 'Perlu Approve — HGL',
                    'code'   => $b->code ?: '—',
                    'cabang' => $b->cabang,
                    'note'   => null,
                    'time'   => $b->time,
                    'icon'   => 'fa-bowl-rice',
                    'color'  => 'text-green-400',
                    'url'    => route('view.foto.beras', $b->id),
                ]);
            }

            $bast = DB::table('ref_bast_status as b')
                ->leftJoin('ref_cabang as r', 'b.code_cabang', '=', 'r.code_cabang')
                ->where('b.status', 'BELUM DIBAYAR')->orderByDesc('b.created_at')->limit(6)
                ->get(['b.nomor_surat', 'b.jenis', 'b.created_at as time', 'r.name_cabang as cabang']);

            foreach ($bast as $bt) {
                $recent->push([
                    'label'  => 'Belum Dibayar — BAST ' . $bt->jenis,
                    'code'   => $bt->nomor_surat ?: '(tanpa no. surat)',
                    'cabang' => $bt->cabang,
                    'note'   => null,
                    'time'   => $bt->time,
                    'icon'   => 'fa-money-check-dollar',
                    'color'  => 'text-blue-400',
                    'url'    => route('status.bayar.bast'),
                ]);
            }

            $recent = $recent->sortByDesc(fn ($item) => $item['time'])->take(6)->values();
        }

        return [$count, $recent];
    }

    /**
     * ADMIN CABANG (Inspektor) — hanya dokumen HPK yang DITOLAK (status 'Reject').
     * Pakai Eloquent model agar CabangScope otomatis membatasi ke cabang user.
     * Klik item -> halaman edit untuk diperbaiki sesuai catatan.
     */
    private function forInspektor(): array
    {
        $rejGabah = MasHpkkGabah::where('status_data', 'Reject')->count();
        $rejBeras = MasHpkkBeras::where('status', 'Reject')->count();

        $count  = $rejGabah + $rejBeras;
        $recent = collect();

        if ($count > 0) {
            $gabah = MasHpkkGabah::where('status_data', 'Reject')
                ->orderByDesc('id_hpkk_gabah')->limit(6)
                ->get(['id_hpkk_gabah', 'nomor_hpkk_gabah', 'catatan', 'tanggal_pelaksanaan']);

            foreach ($gabah as $g) {
                $recent->push([
                    'label'  => 'Ditolak — GKP',
                    'code'   => $g->nomor_hpkk_gabah ?: '—',
                    'cabang' => null,
                    'note'   => $g->catatan,
                    'time'   => $g->tanggal_pelaksanaan,
                    'icon'   => 'fa-wheat-awn',
                    'color'  => 'text-red-400',
                    'url'    => route('edit.gabah', $g->id_hpkk_gabah),
                ]);
            }

            $beras = MasHpkkBeras::where('status', 'Reject')
                ->orderByDesc('id_hpkk_beras')->limit(6)
                ->get(['id_hpkk_beras', 'nomor_hpkk_beras', 'catatan', 'tanggal_pemeriksaan']);

            foreach ($beras as $b) {
                $recent->push([
                    'label'  => 'Ditolak — HGL',
                    'code'   => $b->nomor_hpkk_beras ?: '—',
                    'cabang' => null,
                    'note'   => $b->catatan,
                    'time'   => $b->tanggal_pemeriksaan,
                    'icon'   => 'fa-bowl-rice',
                    'color'  => 'text-red-400',
                    'url'    => route('edit.beras', $b->id_hpkk_beras),
                ]);
            }

            $recent = $recent->sortByDesc(fn ($item) => $item['time'])->take(6)->values();
        }

        return [$count, $recent];
    }
}
