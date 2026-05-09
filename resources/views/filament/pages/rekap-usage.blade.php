<x-filament-panels::page>
    <div class="space-y-8">
        
        {{-- 1. HEADER STATS (WIDGETS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-50 from-blue-600 to-blue-700 rounded-3xl p-6 text-yellow shadow-xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Total Pemakaian</p>
                        <h3 class="text-3xl font-black mt-1">{{ number_format($this->getUsageData()->sum('total_semua')) }}</h3>
                        <p class="text-blue-200 text-[10px] mt-1 italic">Lembar terhitung bulan ini</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-2xl"><x-heroicon-s-chart-bar class="w-6 h-6"/></div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Rata-rata Tertinggi</p>
                        <h3 class="text-3xl font-black mt-1">{{ number_format($this->getUsageData()->max('rata_rata')) }}</h3>
                        <p class="text-emerald-200 text-[10px] mt-1 italic">Lembar / Bulan</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-2xl"><x-heroicon-s-bolt class="w-6 h-6"/></div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-3xl p-6 text-white shadow-xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-purple-100 text-xs font-bold uppercase tracking-wider">Unit Teraktif</p>
                        <h3 class="text-xl font-black mt-2 uppercase truncate w-40">{{ $this->getUsageData()->first()->nama_customer ?? '-' }}</h3>
                        <p class="text-purple-200 text-[10px] mt-1 italic">Peringkat 1 bulan ini</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-2xl"><x-heroicon-s-trophy class="w-6 h-6"/></div>
                </div>
            </div>
        </div>

        {{-- 2. CONTROL CENTER --}}
        <div class="bg-slate-9 rounded-3xl shadow-sm border bg-slate-9 p-2 flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 flex gap-2 p-2">
                <select wire:model.live="month" class="bg-slate-9 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
                <select wire:model.live="year" class="bg-slate-9 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500">
                    @foreach(range(date('Y'), 2024) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-2 w-full md:w-auto">
                <a href="{{ route('cetak.top-usage', ['month' => $month, 'year' => $year]) }}" target="_blank" 
                   class="flex items-center justify-center gap-2 px-8 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                   <x-heroicon-o-printer class="w-4 h-4"/>
                   Generate Report PDF
                </a>
            </div>
        </div>

        {{-- 3. THE SMART TABLE --}}
        <div class="bg-slate-50 rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100">
                        <th class="p-5 text-[10px] font-black uppercase text-slate-400 tracking-tighter text-center">Rank</th>
                        <th class="p-5 text-[10px] font-black uppercase text-slate-400 tracking-tighter">Customer & Unit</th>
                        <th class="p-5 text-[10px] font-black uppercase text-slate-400 tracking-tighter text-center">Production Details</th>
                        <th class="p-5 text-[10px] font-black uppercase text-slate-400 tracking-tighter text-center">Efficiency Score</th>
                        <th class="p-5 text-[10px] font-black uppercase text-slate-400 tracking-tighter text-right">Quick Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($this->getUsageData() as $index => $row)
                        <tr class="group hover:bg-blue-50/30 transition-all">
                            <td class="p-5 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-2xl font-black {{ $index < 3 ? 'text-blue-600' : 'text-slate-300' }}">#{{ $index + 1 }}</span>
                                    @if($index == 0) <span class="text-[8px] font-bold text-amber-500 uppercase">Top Tier</span> @endif
                                </div>
                            </td>
                            <td class="p-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center font-black text-slate-500">
                                        {{ substr($row->nama_customer, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-800 text-sm uppercase tracking-tight">{{ $row->nama_customer }}</h4>
                                        <p class="text-[10px] text-slate-400 font-medium">SN: {{ $row->serial_number }} • {{ $row->tipe_model }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5">
                                <div class="flex justify-center gap-4">
                                    <div class="text-center">
                                        <span class="text-[10px] block font-bold text-blue-500 uppercase">Black</span>
                                        <span class="font-black text-slate-700">{{ number_format($row->total_bw) }}</span>
                                    </div>
                                    <div class="text-center border-l pl-4">
                                        <span class="text-[10px] block font-bold text-rose-500 uppercase">Color</span>
                                        <span class="font-black text-slate-700">{{ number_format($row->total_color) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5 text-center">
                                <div class="inline-block px-4 py-2 bg-emerald-50 rounded-2xl border border-emerald-100">
                                    <span class="text-sm font-black text-emerald-600 block leading-none">{{ number_format($row->rata_rata) }}</span>
                                    <span class="text-[8px] font-bold text-emerald-400 uppercase tracking-tighter">Avg/Month</span>
                                </div>
                            </td>
                            <td class="p-5 text-right">
                                <button class="p-2 hover:bg-white rounded-xl border border-transparent hover:border-gray-200 hover:shadow-sm transition-all text-slate-400 hover:text-blue-600" title="Scan QR Mesin">
                                    <x-heroicon-o-qr-code class="w-5 h-5"/>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>