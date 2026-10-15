<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineEvent;
use App\Models\ServiceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MachineRiwayatController extends Controller
{
    private const NAMA_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Isi nama kolom MrcContract yang pasti di sini jika deteksi otomatis salah.
     * Contoh: 'sewa' => 'harga_sewa_bulanan'
     */
    private const KONTRAK_KOLOM = [
        'sewa'        => null,
        'free_bw'     => null,
        'free_color'  => null,
        'harga_bw'    => null,
        'harga_color' => null,
        'ppn'         => null,
    ];

    // Kata kunci pencarian kolom kontrak (semua kata dalam satu grup harus ada di nama kolom)
    private const KONTRAK_CARI = [
        'sewa'        => [['sewa']],
        'free_bw'     => [['free', 'bw'], ['free', 'hitam'], ['gratis', 'bw'], ['free', 'copy'], ['free']],
        'free_color'  => [['free', 'color'], ['free', 'warna'], ['gratis', 'color']],
        'harga_bw'    => [['harga', 'bw'], ['harga', 'hitam'], ['tarif', 'bw'], ['over', 'bw'], ['lebih', 'bw'], ['harga', 'copy'], ['harga', 'lembar']],
        'harga_color' => [['harga', 'color'], ['harga', 'warna'], ['tarif', 'color'], ['over', 'color'], ['lebih', 'color']],
        'ppn'         => [['ppn']],
    ];

    public function show(Request $request, Machine $machine)
    {
        // ===== Filter tahun: ?tahun=2026 (default tahun berjalan) atau ?tahun=semua =====
        $pilih = $request->query('tahun');
        $tahun = ($pilih === 'semua') ? null : (int) ($pilih ?: now()->year);
        $periodeLabel = $tahun ? (string) $tahun : 'Semua Tahun';

        $events = MachineEvent::with(['customer', 'technician', 'sparepart'])
            ->where('machine_id', $machine->id)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $logsMesin = ServiceLog::where('machine_id', $machine->id)
            ->get(['id', 'tanggal', 'counter_bw', 'counter_color']);
        $logs = $logsMesin->keyBy('id');

        // Counter tiap event diambil dari Service Log asalnya (BW & Color)
        foreach ($events as $e) {
            $log = $e->service_log_id ? $logs->get($e->service_log_id) : null;

            $e->cbw    = $log?->counter_bw ?: ($e->counter ?: null);
            $e->ccolor = $log?->counter_color ?: null;
        }

        // Siklus (dihitung dari semua event): bertambah tiap mesin dikirim ke customer
        $siklus = 0;
        foreach ($events as $e) {
            if ($e->tipe === 'kirim_customer') {
                $siklus++;
            }
            $e->siklus = $siklus;
        }

        // Daftar tahun untuk dropdown filter
        $daftarTahun = $logsMesin->pluck('tanggal')->filter()
            ->map(fn($t) => Carbon::parse($t)->year)
            ->merge($events->map(fn($e) => $e->tanggal->year))
            ->push(now()->year)
            ->unique()->sortDesc()->values();

        // ===== Event pada tahun terpilih (untuk riwayat kejadian) =====
        $eventsTahun = $events
            ->filter(fn($e) => !$tahun || $e->tanggal->year === $tahun)
            ->values();

        $kembar = $eventsTahun
            ->groupBy(fn($e) => $e->tanggal->toDateString())
            ->filter(fn($g) => $g->count() > 1);

        $urutKembar = $kembar->keys()->values();

        foreach ($eventsTahun as $e) {
            $k = $e->tanggal->toDateString();
            $e->grup_tgl = $kembar->has($k) ? $urutKembar->search($k) : null;
            $e->jml_tgl  = $kembar->has($k) ? $kembar[$k]->count() : 1;
        }

        $dobel = $eventsTahun
            ->where('tipe', 'service')
            ->groupBy(fn($e) => $e->tanggal->toDateString() . '|' . ($e->keterangan ?? '') . '|' . ($e->cbw ?? ''))
            ->filter(fn($g) => $g->count() > 1);

        foreach ($dobel as $grup) {
            foreach ($grup as $e) {
                $e->dobel = true;
            }
        }

        // ===== Riwayat penggantian part (selisih dihitung dari SEMUA data, ditampilkan per tahun) =====
        $semuaGanti = $events
            ->where('tipe', 'ganti_part')
            ->whereNotNull('sparepart_id')
            ->groupBy('sparepart_id')
            ->flatMap(function ($g) {
                $nama = $g->first()->sparepart->nama_sparepart ?? '-';

                $rows = $g
                    ->groupBy(fn($e) => $e->service_log_id ?: 'e' . $e->id)
                    ->map(function ($items) use ($nama) {
                        $first = $items->first();

                        return (object) [
                            'sparepart_id' => $first->sparepart_id,
                            'part'         => $nama,
                            'tanggal'      => $first->tanggal,
                            'qty'          => $items->sum('jumlah'),
                            'bw'           => $first->cbw,
                            'color'        => $first->ccolor,
                            'teknisi'      => $first->teknisi_nama,
                            'selisih_bw'   => null,
                            'selisih_clr'  => null,
                            'hari'         => null,
                        ];
                    })
                    ->sortBy(fn($r) => $r->tanggal->timestamp)
                    ->values();

                $rows->each(function ($r, $i) use ($rows) {
                    if ($i === 0) return;

                    $prev = $rows[$i - 1];

                    if ($r->bw && $prev->bw) {
                        $r->selisih_bw = $r->bw - $prev->bw;
                    }
                    if ($r->color && $prev->color) {
                        $r->selisih_clr = $r->color - $prev->color;
                    }

                    $r->hari = (int) abs($prev->tanggal->diffInDays($r->tanggal));
                });

                return $rows;
            })
            ->values();

        $gantiTahun = $semuaGanti
            ->filter(fn($r) => !$tahun || $r->tanggal->year === $tahun)
            ->sort(fn($a, $b) => [$a->tanggal->timestamp, $a->part] <=> [$b->tanggal->timestamp, $b->part])
            ->values();

        $totalPart = $gantiTahun
            ->groupBy('sparepart_id')
            ->map(function ($g) {
                $valid = $g->pluck('selisih_bw')->filter(fn($v) => $v !== null && $v >= 0);

                return (object) [
                    'nama'     => $g->first()->part,
                    'total'    => $g->sum('qty'),
                    'kali'     => $g->count(),
                    'terakhir' => $g->max('tanggal'),
                    'avg_bw'   => $valid->isNotEmpty() ? (int) round($valid->avg()) : null,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $grandPart     = $gantiTahun->sum('qty');
        $adaColorPart  = $gantiTahun->contains(fn($r) => !empty($r->color));

        // ===== Pemakaian counter per bulan + tagihan =====
        $kontrak   = $this->bacaKontrak($machine);
        $pemakaian = $this->hitungPemakaian($logsMesin);

        if ($tahun) {
            $deret = collect(range(1, 12))->map(function ($m) use ($tahun, $pemakaian) {
                $key = sprintf('%d-%02d', $tahun, $m);
                return $pemakaian->rows->get($key) ?? $this->barisKosong($key);
            });
        } else {
            $deret = $pemakaian->rows->values();
        }

        $deret->each(function ($r) use ($kontrak) {
            if (!$r->kosong) {
                $r->tagihan = $this->hitungTagihan($r, $kontrak);
            }
        });

        $baris = $deret->filter(fn($r) => !$r->kosong);
        $penuh = $baris->where('penuh', true);

        $tot = (object) [
            'bw'       => $baris->sum('bw'),
            'color'    => $baris->sum('color'),
            'sewa'     => $baris->sum(fn($r) => $r->tagihan->sewa ?? 0),
            'biaya'    => $baris->sum(fn($r) => $r->tagihan->biaya ?? 0),
            'subtotal' => $baris->sum(fn($r) => $r->tagihan->subtotal ?? 0),
            'ppn'      => $baris->sum(fn($r) => $r->tagihan->ppn ?? 0),
            'total'    => $baris->sum(fn($r) => $r->tagihan->total ?? 0),
        ];

        $rata = (object) [
            'n'     => $penuh->count(),
            'bw'    => $penuh->isNotEmpty() ? (int) round($penuh->avg('bw')) : null,
            'color' => $penuh->isNotEmpty() ? (int) round($penuh->avg('color')) : null,
            'total' => $penuh->isNotEmpty() ? (int) round($penuh->avg(fn($r) => $r->tagihan->total ?? 0)) : null,
        ];

        $bulanIni = $baris->firstWhere('key', now()->format('Y-m'));

        // Tipe / model mesin (cari kolom merk & model/tipe di tabel machines)
        $tipeMesin = collect([['merk', 'brand'], ['model', 'tipe', 'type', 'nama']])
            ->map(function ($kata) use ($machine) {
                foreach ($kata as $k) {
                    foreach ($machine->getAttributes() as $key => $v) {
                        $lk = strtolower($key);
                        if (
                            str_contains($lk, $k)
                            && !str_ends_with($lk, '_id') && !str_ends_with($lk, '_at')
                            && is_scalar($v) && filled($v)
                        ) {
                            return (string) $v;
                        }
                    }
                }
                return null;
            })
            ->filter()->unique()->implode(' ') ?: null;

        return view('cetak.riwayat-mesin', [
            'machine'       => $machine->load('customer'),
            'tipeMesin'     => $tipeMesin,
            'kontrak'       => $kontrak,
            'tahunDipilih'  => $tahun,
            'periodeLabel'  => $periodeLabel,
            'daftarTahun'   => $daftarTahun,

            'events'        => $eventsTahun,
            'jumlahService' => $eventsTahun->where('tipe', 'service')->count(),
            'statusAkhir'   => $events->last()?->tipe_label ?? '-',
            'tanggalAwal'   => $events->first()?->tanggal,
            'jumlahKembar'  => $kembar->count(),
            'jumlahDobel'   => $dobel->count(),

            'deret'         => $deret,
            'tot'           => $tot,
            'rata'          => $rata,
            'bulanIni'      => $bulanIni,
            'adaColor'      => $baris->contains(fn($r) => $r->color > 0),
            'anomali'       => $pemakaian->anomali,

            'gantiTahun'    => $gantiTahun,
            'totalPart'     => $totalPart,
            'grandPart'     => $grandPart,
            'adaColorPart'  => $adaColorPart,
        ]);
    }

    /**
     * Pemakaian counter per bulan.
     * Pemakaian di antara dua pembacaan counter dibagi rata per hari,
     * lalu dijumlahkan per bulan (bulan tanpa kunjungan = estimasi).
     */
    private function hitungPemakaian($logs): object
    {
        $perTgl = $logs
            ->filter(fn($l) => $l->tanggal)
            ->groupBy(fn($l) => Carbon::parse($l->tanggal)->toDateString())
            ->map(fn($g, $tgl) => (object) [
                'tgl'   => Carbon::parse($tgl)->startOfDay(),
                'bw'    => $g->max('counter_bw') ?: null,
                'color' => $g->max('counter_color') ?: null,
            ])
            ->sortBy(fn($r) => $r->tgl->timestamp)
            ->values();

        $bulan = [];
        $ambil = function (string $key) use (&$bulan) {
            if (!isset($bulan[$key])) {
                $bulan[$key] = (object) ['bw' => 0.0, 'color' => 0.0, 'hari' => 0, 'baca' => 0];
            }
            return $bulan[$key];
        };

        foreach ($perTgl as $r) {
            $ambil($r->tgl->format('Y-m'))->baca++;
        }

        $anomali = 0;

        foreach (['bw', 'color'] as $jenis) {
            $prev = null;

            foreach ($perTgl as $r) {
                if ($r->$jenis === null) continue;

                if ($prev) {
                    $hari = (int) $prev->tgl->diffInDays($r->tgl);

                    if ($r->$jenis < $prev->$jenis) {
                        $anomali++; // counter mundur, dilewati
                    } elseif ($hari > 0) {
                        $rate = ($r->$jenis - $prev->$jenis) / $hari;

                        for ($i = 1; $i <= $hari; $i++) {
                            $m = $ambil($prev->tgl->copy()->addDays($i)->format('Y-m'));
                            $m->$jenis += $rate;

                            if ($jenis === 'bw') {
                                $m->hari++;
                            }
                        }
                    }
                }

                $prev = $r;
            }
        }

        ksort($bulan);

        $rows = collect($bulan)
            ->map(function ($m, $key) {
                $awal      = Carbon::createFromFormat('Y-m-d', $key . '-01')->startOfDay();
                $hariBulan = $awal->daysInMonth;
                $penuh     = $m->hari >= $hariBulan;

                return (object) [
                    'key'          => $key,
                    'label'        => self::NAMA_BULAN[$awal->month] . ' ' . $awal->year,
                    'baca'         => $m->baca,
                    'hari'         => $m->hari,
                    'hari_bulan'   => $hariBulan,
                    'penuh'        => $penuh,
                    'bw'           => (int) round($m->bw),
                    'color'        => (int) round($m->color),
                    'proyeksi_bw'  => (!$penuh && $m->hari > 0) ? (int) round($m->bw / $m->hari * $hariBulan) : null,
                    'status'       => !$penuh ? 'Parsial' : ($m->baca > 0 ? 'Terukur' : 'Estimasi'),
                    'kosong'       => false,
                    'tagihan'      => null,
                ];
            })
            ->filter(fn($r) => $r->hari > 0);

        return (object) ['rows' => $rows, 'anomali' => $anomali];
    }

    private function barisKosong(string $key): object
    {
        $awal = Carbon::createFromFormat('Y-m-d', $key . '-01')->startOfDay();

        return (object) [
            'key'         => $key,
            'label'       => self::NAMA_BULAN[$awal->month] . ' ' . $awal->year,
            'baca'        => 0,
            'hari'        => 0,
            'hari_bulan'  => $awal->daysInMonth,
            'penuh'       => false,
            'bw'          => 0,
            'color'       => 0,
            'proyeksi_bw' => null,
            'status'      => '-',
            'kosong'      => true,
            'tagihan'     => null,
        ];
    }

    /**
     * Tagihan bulanan = harga sewa + (pemakaian di atas free copy x harga per lembar), lalu + PPN.
     */
    private function hitungTagihan(object $r, ?object $k): ?object
    {
        if (!$k || $k->sewa === null) return null;

        $freeBw  = $k->free_bw ?? 0;
        $freeClr = $k->free_color ?? 0;

        $lebihBw  = max(0, $r->bw - $freeBw);
        $lebihClr = max(0, $r->color - $freeClr);

        $biaya = $lebihBw * ($k->harga_bw ?? 0) + $lebihClr * ($k->harga_color ?? 0);
        $sub   = $k->sewa + $biaya;
        $ppn   = $sub * ($k->ppn / 100);

        return (object) [
            'free_bw'   => (int) $freeBw,
            'free_clr'  => (int) $freeClr,
            'lebih_bw'  => (int) $lebihBw,
            'lebih_clr' => (int) $lebihClr,
            'sewa'      => (int) round($k->sewa),
            'biaya'     => (int) round($biaya),
            'subtotal'  => (int) round($sub),
            'ppn'       => (int) round($ppn),
            'total'     => (int) round($sub + $ppn),
        ];
    }

    /**
     * Ambil data kontrak MRC terbaru untuk mesin ini. Nama kolom dideteksi otomatis
     * (atau pakai KONTRAK_KOLOM jika diisi).
     */
    private function bacaKontrak(Machine $machine): ?object
    {
        if (!class_exists(\App\Models\MrcContract::class)) return null;

        $model = \App\Models\MrcContract::where('machine_id', $machine->id)->latest('id')->first();
        if (!$model) return null;

        $atr    = $model->getAttributes();
        $hasil  = [];
        $sumber = [];

        foreach (self::KONTRAK_CARI as $field => $grup) {
            $nama = self::KONTRAK_KOLOM[$field]
                ?? $this->cariKolom($atr, $grup, str_ends_with($field, '_bw'));

            $hasil[$field]  = ($nama && isset($atr[$nama]) && is_numeric($atr[$nama])) ? (float) $atr[$nama] : null;
            $sumber[$field] = $nama;
        }

        // PPN: 11 (persen), 0.11 (pecahan), atau 1/0 (kena PPN atau tidak)
        $p = $hasil['ppn'];
        if ($p === null)            $p = 0.0;
        elseif ($p > 0 && $p < 1)   $p = $p * 100;
        elseif ($p == 1)            $p = 11.0;
        elseif ($p > 100)           $p = 0.0;
        $hasil['ppn'] = $p;

        $noKontrak = null;
        foreach (['no_kontrak', 'nomor_kontrak', 'no_sko'] as $k) {
            if (isset($atr[$k]) && filled($atr[$k])) {
                $noKontrak = (string) $atr[$k];
                break;
            }
        }

        return (object) ($hasil + ['sumber' => $sumber, 'no_kontrak' => $noKontrak]);
    }

    private function cariKolom(array $atr, array $grup, bool $hindariWarna): ?string
    {
        $abaikan = ['tgl', 'tanggal', 'masa', 'periode', 'lama', 'mulai', 'akhir', 'durasi', 'status', 'nomor', 'no_'];

        foreach ($grup as $kata) {
            foreach ($atr as $key => $val) {
                $k = strtolower($key);

                if (str_ends_with($k, '_id') || str_ends_with($k, '_at')) continue;
                if (!is_numeric($val)) continue;
                if ($hindariWarna && (str_contains($k, 'color') || str_contains($k, 'warna'))) continue;

                foreach ($abaikan as $x) {
                    if (str_contains($k, $x)) continue 2;
                }

                if (collect($kata)->every(fn($w) => str_contains($k, $w))) {
                    return $key;
                }
            }
        }

        return null;
    }
}