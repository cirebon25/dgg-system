<?php

namespace App\Console\Commands;

use App\Models\AttendanceDailyResult;
use App\Models\AttendanceEmployee;
use App\Models\AttendanceLog;
use App\Models\AttendanceShift;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncFingerspot extends Command
{
    protected $signature = 'fingerspot:sync
                            {--since= : Tarik ulang log scan mulai tanggal ini (Y-m-d H:i:s)}
                            {--full : Salin ulang seluruh hasil harian (shift_result)}
                            {--chunk=500 : Jumlah baris per upsert}';

    protected $description = 'Sinkronisasi log scan, pegawai, jam kerja, dan rincian harian dari Fingerspot Personnel';

    public function handle(): int
    {
        try {
            $employees = $this->syncEmployees();
            $shifts    = $this->syncShifts();
            $daily     = $this->syncDailyResults();
            $logs      = $this->syncLogs();

            $this->info("Selesai. Pegawai: {$employees}, shift: {$shifts}, rincian harian: {$daily}, log: {$logs}.");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Sync gagal: ' . $e->getMessage());
            report($e);

            return self::FAILURE;
        }
    }

    private function db()
    {
        return DB::connection('fingerspot');
    }

    private function chunkSize(): int
    {
        return max(1, (int) $this->option('chunk'));
    }

    /** Datetime MySQL kosong ("0000-00-00 00:00:00") dijadikan null. */
    private function dt(?string $value): ?string
    {
        if (! $value || str_starts_with($value, '0000')) {
            return null;
        }

        return Carbon::parse($value)->toDateTimeString();
    }

    private function syncEmployees(): int
    {
        $now = now();

        $rows = $this->db()
            ->table('pegawai as p')
            ->leftJoin('pembagian1 as a', 'a.pembagian1_id', '=', 'p.pembagian1_id')
            ->leftJoin('pembagian2 as b', 'b.pembagian2_id', '=', 'p.pembagian2_id')
            ->leftJoin('pembagian3 as c', 'c.pembagian3_id', '=', 'p.pembagian3_id')
            ->select([
                'p.pegawai_id',
                'p.pegawai_pin',
                'p.pegawai_nip',
                'p.pegawai_nama',
                'p.pegawai_status',
                'a.pembagian1_nama as jabatan',
                'b.pembagian2_nama as departemen',
                'c.pembagian3_nama as kantor',
            ])
            ->get()
            ->map(fn($r) => [
                'pegawai_id' => (int) $r->pegawai_id,
                'pin'        => trim((string) $r->pegawai_pin),
                'nip'        => $r->pegawai_nip,
                'name'       => $r->pegawai_nama,
                'status'     => (int) $r->pegawai_status,
                'jabatan'    => $r->jabatan,
                'departemen' => $r->departemen,
                'kantor'     => $r->kantor,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($rows) {
            AttendanceEmployee::upsert(
                $rows,
                ['pin'],
                ['pegawai_id', 'nip', 'name', 'status', 'jabatan', 'departemen', 'kantor', 'updated_at']
            );
        }

        return count($rows);
    }

    private function syncShifts(): int
    {
        $now = now();

        $rows = $this->db()
            ->table('jam_kerja')
            ->select(['jk_id', 'jk_name', 'jk_bcin', 'jk_cin', 'jk_tol_late'])
            ->get()
            ->map(fn($r) => [
                'jk_id'      => (int) $r->jk_id,
                'name'       => $r->jk_name,
                // jam masuk = batas awal scan masuk + menit (mis. 05:00 + 180 = 08:00)
                'jam_masuk'  => Carbon::createFromTimeString($r->jk_bcin)->addMinutes((int) $r->jk_cin)->format('H:i:s'),
                'tol_late'   => (int) $r->jk_tol_late,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($rows) {
            AttendanceShift::upsert($rows, ['jk_id'], ['name', 'jam_masuk', 'tol_late', 'updated_at']);
        }

        return count($rows);
    }

    private function syncDailyResults(): int
    {
        // Personnel bisa menghitung ulang hari-hari terakhir, jadi tarik ulang 45 hari ke belakang.
        $since = (! $this->option('full') && AttendanceDailyResult::exists())
            ? now()->subDays(45)->toDateString()
            : null;

        $query = $this->db()
            ->table('shift_result')
            ->where('khusus_lembur', 0)
            ->where('khusus_extra', 0)
            ->where('temp_id_auto', 0)
            ->when($since, fn($q) => $q->where('tgl_shift', '>=', $since))
            ->orderBy('pegawai_id')
            ->orderBy('tgl_shift');

        $chunk  = $this->chunkSize();
        $buffer = [];
        $total  = 0;

        foreach ($query->cursor() as $r) {
            $now = now();

            $buffer[] = [
                'pegawai_id'        => (int) $r->pegawai_id,
                'tgl_shift'         => $r->tgl_shift,
                'jk_id'             => (int) $r->jk_id,
                'libur_umum'        => (int) ($r->libur_umum != 0),
                'libur_rutin'       => (int) ($r->libur_rutin != 0),
                'jk_ot'             => (int) ($r->jk_ot != 0),
                'scan_in'           => $this->dt($r->scan_in),
                'scan_out'          => $this->dt($r->scan_out),
                'late_minute'       => (int) $r->late_minute,
                'early_minute'      => (int) $r->early_minute,
                'durasi_minute'     => (int) $r->durasi_minute,
                'durasi_eot_minute' => (int) $r->durasi_eot_minute,
                'break_minute'      => (int) $r->break_minute,
                'break_ot_minute'   => (int) $r->break_ot_minute,
                'status_jk'         => (int) $r->status_jk,
                'keterangan'        => $r->keterangan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            if (count($buffer) >= $chunk) {
                $total += $this->flushDaily($buffer);
                $buffer = [];
            }
        }

        return $total + $this->flushDaily($buffer);
    }

    private function flushDaily(array $rows): int
    {
        if (! $rows) {
            return 0;
        }

        AttendanceDailyResult::upsert(
            $rows,
            ['pegawai_id', 'tgl_shift'],
            [
                'jk_id',
                'libur_umum',
                'libur_rutin',
                'jk_ot',
                'scan_in',
                'scan_out',
                'late_minute',
                'early_minute',
                'durasi_minute',
                'durasi_eot_minute',
                'break_minute',
                'break_ot_minute',
                'status_jk',
                'keterangan',
                'updated_at',
            ]
        );

        return count($rows);
    }

    private function syncLogs(): int
    {
        $since = $this->resolveSince();
        $chunk = $this->chunkSize();

        $query = $this->db()
            ->table('att_log')
            ->select(['sn', 'scan_date', 'pin', 'verifymode', 'inoutmode', 'work_code', 'att_id'])
            ->when($since, fn($q) => $q->where('scan_date', '>=', $since))
            ->orderBy('scan_date')
            ->orderBy('pin');

        $buffer = [];
        $total  = 0;

        foreach ($query->cursor() as $row) {
            $buffer[] = $this->mapLog($row);

            if (count($buffer) >= $chunk) {
                $total += $this->flushLogs($buffer);
                $buffer = [];
            }
        }

        return $total + $this->flushLogs($buffer);
    }

    /** Mulai dari scan terakhir dikurangi 1 hari, supaya log yang telat ditarik Personnel tetap terambil. */
    private function resolveSince(): ?string
    {
        if ($opt = $this->option('since')) {
            return Carbon::parse($opt)->toDateTimeString();
        }

        $last = AttendanceLog::max('scan_at');

        return $last ? Carbon::parse($last)->subDay()->toDateTimeString() : null;
    }

    private function mapLog(object $row): array
    {
        $now = now();

        return [
            'sn'          => trim((string) $row->sn),
            'pin'         => trim((string) $row->pin),
            'scan_at'     => Carbon::parse($row->scan_date)->toDateTimeString(),
            'verify_mode' => (int) $row->verifymode,
            'in_out_mode' => (int) $row->inoutmode,
            'work_code'   => (int) $row->work_code,
            'att_id'      => $row->att_id,
            'created_at'  => $now,
            'updated_at'  => $now,
        ];
    }

    private function flushLogs(array $rows): int
    {
        if (! $rows) {
            return 0;
        }

        AttendanceLog::upsert(
            $rows,
            ['sn', 'pin', 'scan_at'],
            ['verify_mode', 'in_out_mode', 'work_code', 'att_id', 'updated_at']
        );

        return count($rows);
    }
}