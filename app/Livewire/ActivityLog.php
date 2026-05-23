<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $event_filter = '';
    public string $tgl_mulai   = '';
    public string $tgl_akhir   = '';

    public function mount(): void
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');
    }

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingEventFilter(): void  { $this->resetPage(); }
    public function updatingTglMulai(): void     { $this->resetPage(); }
    public function updatingTglAkhir(): void     { $this->resetPage(); }

    public function render()
    {
        $query = DB::table('activity_log as a')
            ->leftJoin('mas_user as u', 'a.causer_id', '=', 'u.id_user')
            ->select(
                'a.id', 'a.log_name', 'a.description', 'a.subject_type',
                'a.subject_id', 'a.properties', 'a.created_at',
                'u.nama as causer_nama', 'u.level as causer_level',
                'u.group as causer_group'
            )
            ->orderBy('a.created_at', 'desc');

        if ($this->tgl_mulai && $this->tgl_akhir) {
            $query->whereBetween('a.created_at', [
                $this->tgl_mulai . ' 00:00:00',
                $this->tgl_akhir . ' 23:59:59',
            ]);
        }

        if ($this->event_filter) {
            $query->where('a.description', $this->event_filter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('u.nama',          'like', "%{$this->search}%")
                  ->orWhere('a.description', 'like', "%{$this->search}%")
                  ->orWhere('a.properties',  'like', "%{$this->search}%");
            });
        }

        $events = DB::table('activity_log')
            ->select('description')
            ->distinct()
            ->orderBy('description')
            ->pluck('description');

        return view('livewire.activity-log', [
            'logs'   => $query->paginate(20),
            'events' => $events,
        ]);
    }
}
