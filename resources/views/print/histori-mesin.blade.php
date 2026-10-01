@extends('print.layout')

@section('title', 'Histori Mesin - ' . $machine->serial_number)
@section('body-class', 'bg-slate-100 text-slate-800 pb-10 font-sans')

@push('head')
    <script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
    <div class="bg-slate-900 text-white p-5 shadow-md sticky top-0 z-50">
        <div class="max-w-md mx-auto">
            <span class="text-[10px] font-bold uppercase bg-blue-600 px-2 py-0.5 rounded text-white">Daftar Histori
                Unit</span>
            <h1 class="text-xl font-bold mt-1 text-yellow-400">{{ $machine->tipe_model }}</h1>
            <p class="text-xs opacity-90 font-mono">Serial Number: <b>{{ $machine->serial_number }}</b></p>
            <p class="text-xs mt-2">Status Saat Ini:
                <span class="px-2 py-0.5 rounded bg-green-600 text-white font-bold text-[10px]">{{ $machine->status }}</span>
            </p>
        </div>
    </div>

    <div class="max-w-md mx-auto px-4 mt-5 space-y-5">

        {{-- Log Service --}}
        <div>
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">🔧 Jurnal Perbaikan &amp; Log Service
            </h2>

            @forelse ($serviceLogs as $log)
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-amber-500 mb-3">
                    <div class="flex justify-between text-[10px] text-slate-400 font-bold">
                        <span>🛠️ Teknisi: {{ $log->nama_technician ?? '-' }}</span>
                        <span>📅 {{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y H:i') }}</span>
                    </div>
                    <div class="mt-2 text-xs"><b class="text-slate-700">Kendala:</b> <span
                            class="text-slate-600">{{ $log->keluhan }}</span></div>
                    <div class="mt-1 text-xs"><b class="text-slate-700">Tindakan:</b> <span
                            class="text-slate-600">{{ $log->tindakan }}</span></div>
                </div>
            @empty
                <div class="bg-white p-4 rounded-xl shadow-sm text-center text-slate-400 text-xs">Belum ada catatan log
                    perbaikan.</div>
            @endforelse
        </div>

        {{-- Riwayat Penempatan --}}
        <div>
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">📍 Riwayat Penempatan Pelanggan</h2>

            @forelse ($deployments as $dep)
                <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-600 mb-3">
                    <div class="text-sm font-bold text-slate-800">{{ $dep->nama_customer }}</div>
                    <div class="text-xs text-slate-500">📍 Lokasi: {{ $dep->kota }}</div>
                    <div class="grid grid-cols-2 gap-2 mt-3 pt-2 border-t border-slate-100 text-[11px] text-slate-600">
                        <div><b>Tanggal
                                Pasang:</b><br>{{ \Carbon\Carbon::parse($dep->tanggal_instal ?? $dep->created_at)->format('d-m-Y') }}
                        </div>
                        <div><b>Counter Awal:</b><br>BW: {{ number_format($dep->counter_bw) }}<br>CL:
                            {{ number_format($dep->counter_color) }}</div>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-2 border-t border-dashed border-slate-100 pt-1">👷 Teknisi
                        Pasang: {{ $dep->nama_technician ?? '-' }}</div>
                </div>
            @empty
                <div class="bg-white p-4 rounded-xl shadow-sm text-center text-slate-400 text-xs">Mesin ini belum pernah
                    dikirim ke customer.</div>
            @endforelse
        </div>

    </div>
@endsection
