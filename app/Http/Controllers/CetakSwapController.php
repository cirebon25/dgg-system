<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakSwapController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        $data = DB::table('machine_replacements')
            ->leftJoin('customers', 'machine_replacements.customer_id', '=', 'customers.id')
            ->leftJoin('machines as m_old', 'machine_replacements.old_machine_id', '=', 'm_old.id')
            ->leftJoin('machines as m_new', 'machine_replacements.new_machine_id', '=', 'm_new.id')
            ->leftJoin('technicians', 'machine_replacements.technician_id', '=', 'technicians.id')
            ->leftJoin('service_logs as log_old', function ($join) {
                $join->on('machine_replacements.old_machine_id', '=', 'log_old.machine_id')
                    ->on('machine_replacements.customer_id', '=', 'log_old.customer_id')
                    ->on('machine_replacements.tanggal', '=', 'log_old.tanggal')
                    ->where('log_old.kerusakan', '=', 'ROLLING OUT');
            })
            ->leftJoin('service_logs as log_new', function ($join) {
                $join->on('machine_replacements.new_machine_id', '=', 'log_new.machine_id')
                    ->on('machine_replacements.customer_id', '=', 'log_new.customer_id')
                    ->on('machine_replacements.tanggal', '=', 'log_new.tanggal')
                    ->where('log_new.kerusakan', '=', 'ROLLING IN');
            })
            // FIX: Hapus leftJoin deployments yang menyebabkan duplikasi.
            // Ambil volt via subquery (ambil 1 deployment terbaru per customer)
            ->leftJoinSub(
                DB::table('deployments')
                    ->select('customer_id', DB::raw('MAX(id) as max_id'))
                    ->groupBy('customer_id'),
                'dep_latest',
                'dep_latest.customer_id',
                '=',
                'machine_replacements.customer_id'
            )
            ->leftJoin('deployments as dep', 'dep.id', '=', 'dep_latest.max_id')
            ->whereMonth('machine_replacements.tanggal', (int) $bulan)
            ->whereYear('machine_replacements.tanggal', (int) $tahun)
            ->select(
                'machine_replacements.id',
                'machine_replacements.tanggal',
                'customers.nama_customer',
                'customers.kota',
                'm_old.serial_number as sn_lama',
                'm_old.tipe_model as tipe_lama',
                'm_new.serial_number as sn_baru',
                'm_new.tipe_model as tipe_baru',
                'technicians.nama_technician',
                'log_old.counter_bw as counter_bw_old',
                'log_old.counter_color as counter_color_old',
                'log_old.perbaikan as alasan_ganti',
                'log_new.counter_bw as counter_bw_new',
                'log_new.counter_color as counter_color_new',
                'dep.volt as volt_mesin'
            )
            ->orderBy('machine_replacements.tanggal', 'desc')
            ->get();

        $namaBulan = \Carbon\Carbon::createFromFormat('m', $bulan)->translatedFormat('F');

        return view('cetak.tukar-guling', compact('data', 'bulan', 'tahun', 'namaBulan'));
    }

    public function sjRolling(Request $request)
    {
        $payload = $request->query('payload');

        if (!$payload) {
            return 'Gagal memuat dokumen! Data Surat Jalan kosong. Silakan ulangi proses rolling dari menu Ganti Mesin.';
        }

        try {
            $dataDecoded = json_decode(base64_decode($payload), true);

            if (!$dataDecoded) {
                return 'Struktur data Surat Jalan rusak, silakan input kembali.';
            }

            return view('cetak.surat-jalan-rolling', [
                'd'        => $dataDecoded,
                'tanggal'  => date('d/m/Y'),
                'nomor_sj' => 'SJ-RR/' . date('Ymd/Hi'),
            ]);
        } catch (\Exception $e) {
            return 'Eror Membaca Payload Data: ' . $e->getMessage();
        }
    }
}
