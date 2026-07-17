<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Catatan revisi (penolakan verifikator) dibaca dari activity_log,
 * bukan dari kolom `catatan` di tabel HPK — kolom itu milik admin cabang
 * dan tidak boleh tertimpa oleh catatan penolakan.
 *
 * Pemanggil tetap melakukan fallback ke kolom `catatan` untuk data yang
 * ditolak SEBELUM perubahan ini (log lama tidak punya record_id).
 */
class CatatanRevisi
{
    public const GKP = 'Reject GKP';
    public const HGL = 'Reject HGL';

    /** Peta [id_record => catatan] dari log penolakan TERBARU per record. */
    public static function peta(string $description, iterable $ids): array
    {
        $ids = collect($ids)->map(fn ($v) => (int) $v)->filter()->unique()->values()->all();
        if (empty($ids)) return [];

        // JSON_UNQUOTE(JSON_EXTRACT(...)) dipakai agar kompatibel MySQL & MariaDB
        $rows = DB::table('activity_log')
            ->where('description', $description)
            ->whereIn(
                DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(properties, '$.record_id')) AS UNSIGNED)"),
                $ids
            )
            ->orderByDesc('id')
            ->get(['properties']);

        $map = [];
        foreach ($rows as $r) {
            $p   = json_decode($r->properties, true) ?: [];
            $rid = (int) ($p['record_id'] ?? 0);
            if ($rid && !array_key_exists($rid, $map)) {
                $map[$rid] = $p['catatan'] ?? null; // baris pertama = log terbaru
            }
        }
        return $map;
    }

    /** Catatan penolakan terbaru untuk satu record, null bila tidak ada. */
    public static function satu(string $description, int $id): ?string
    {
        return static::peta($description, [$id])[$id] ?? null;
    }
}
