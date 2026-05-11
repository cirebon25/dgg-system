<x-filament-panels::page>
    <div class="space-y-8 bg-slate-950 p-6 rounded-3xl min-h-screen"> {{-- Background Utama Gelap --}}
        
        {{-- 1. HEADER STATS (WIDGETS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card 1 --}}
            <div class="bg-slate-900 border border-amber-600/50 rounded-3xl p-6 text-gray-300 shadow-xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Pemakaian</p>
                        <h3 class="text-3xl font-black mt-1 text-amber-500">{{ number_format($this->getUsageData()->sum('total_semua')) }}</h3>
                        <p class="text-gray-600 text-[10px] mt-1 italic">Lembar terhitung bulan ini</p>
                    </div>
                    <div class="p-3 bg-amber-500/10 rounded-2xl text-amber-500"><x-heroicon-s-chart-bar class="w-6 h-6"/></div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-slate-900 border border-amber-600/50 rounded-3xl p-6 text-gray-300 shadow-xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Rata-rata Tertinggi</p>
                        <h3 class="text-3xl font-black mt-1 text-amber-500">{{ number_format($this->getUsageData()->max('rata_rata')) }}</h3>
                        <p class="text-gray-600 text-[10px] mt-1 italic">Lembar / Bulan</p>
                    </div>
                    <div class="p-3 bg-amber-500/10 rounded-2xl text-amber-500"><x-heroicon-s-bolt class="w-6 h-6"/></div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-slate-900 border border-amber-600/50 rounded-3xl p-6 text-gray-300 shadow-xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Unit Teraktif</p>
                        <h3 class="text-xl font-black mt-2 uppercase truncate w-40 text-amber-500">{{ $this->getUsageData()->first()->nama_customer ?? '-' }}</h3>
                        <p class="text-gray-600 text-[10px] mt-1 italic">Peringkat 1 bulan ini</p>
                    </div>
                    <div class="p-3 bg-amber-500/10 rounded-2xl text-amber-500"><x-heroicon-s-trophy class="w-6 h-6"/></div>
                </div>
            </div>
        </div>

        {{-- 2. CONTROL CENTER --}}
        <div class="bg-slate-900 rounded-3xl shadow-sm border border-amber-600/30 p-2 flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 flex gap-2 p-2">
                {{-- Menggunakan Komponen Select Filament untuk Kontrol Warna yang Total --}}
                <div class="flex-1 md:flex-none">
                    <x-filament::input.wrapper class="border-amber-600/40 rounded-2xl overflow-hidden">
                        <x-filament::input.select wire:model.live="month" class="bg-slate-800 text-gray-300 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ sprintf('%02d', $m) }}" class="bg-slate-900 text-gray-300">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div class="flex-1 md:flex-none">
                    <x-filament::input.wrapper class="border-amber-600/40 rounded-2xl overflow-hidden">
                        <x-filament::input.select wire:model.live="year" class="bg-slate-800 text-gray-300 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            @foreach(range(date('Y'), 2024) as $y)
                                <option value="{{ $y }}" class="bg-slate-900 text-gray-300">{{ $y }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
            
            <div class="p-2 w-full md:w-auto">
                <a href="{{ route('cetak.top-usage', ['month' => $month, 'year' => $year]) }}" target="_blank" 
                   class="flex items-center justify-center gap-2 px-8 py-3 bg-amber-600 text-slate-950 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-500 transition-all shadow-lg">
                   <x-heroicon-o-printer class="w-4 h-4"/>
                   Generate Report PDF
                </a>
            </div>
        </div>

        {{-- 3. THE SMART TABLE --}}
        <div class="bg-slate-900 rounded-[2rem] shadow-2xl border border-amber-600/30 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/80 border-b border-amber-600/20">
                        <th class="p-5 text-[10px] font-black uppercase text-gray-500 tracking-tighter text-center">Rank</th>
                        <th class="p-5 text-[10px] font-black uppercase text-gray-500 tracking-tighter">Customer & Unit</th>
                        <th class="p-5 text-[10px] font-black uppercase text-gray-500 tracking-tighter text-center">Production Details</th>
                        <th class="p-5 text-[10px] font-black uppercase text-gray-500 tracking-tighter text-center">Efficiency Score</th>
                        <th class="p-5 text-[10px] font-black uppercase text-gray-500 tracking-tighter text-right">Quick Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-600/10">
                    @foreach($this->getUsageData() as $index => $row)
                        <tr class="group hover:bg-amber-500/5 transition-all">
                            <td class="p-5 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-2xl font-black {{ $index < 3 ? 'text-amber-500' : 'text-gray-700' }}">#{{ $index + 1 }}</span>
                                    @if($index == 0) <span class="text-[8px] font-bold text-amber-600 uppercase">Top Tier</span> @endif
                                </div>
                            </td>
                            <td class="p-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-800 border border-amber-600/20 flex items-center justify-center font-black text-amber-500">
                                        {{ substr($row->nama_customer, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-black text-gray-300 text-sm uppercase tracking-tight">{{ $row->nama_customer }}</h4>
                                        <p class="text-[10px] text-gray-500 font-medium">SN: {{ $row->serial_number }} • {{ $row->tipe_model }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5 text-gray-300">
                                <div class="flex justify-center gap-4">
                                    <div class="text-center">
                                        <span class="text-[10px] block font-bold text-gray-500 uppercase">Black</span>
                                        <span class="font-black">{{ number_format($row->total_bw) }}</span>
                                    </div>
                                    <div class="text-center border-l border-amber-600/20 pl-4">
                                        <span class="text-[10px] block font-bold text-amber-600 uppercase">Color</span>
                                        <span class="font-black">{{ number_format($row->total_color) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5 text-center">
                                <div class="inline-block px-4 py-2 bg-slate-800/50 rounded-2xl border border-amber-600/30">
                                    <span class="text-sm font-black text-amber-500 block leading-none">{{ number_format($row->rata_rata) }}</span>
                                    <span class="text-[8px] font-bold text-gray-500 uppercase tracking-tighter">Avg/Month</span>
                                </div>
                            </td>
                            <td class="p-5 text-right">
                                <button class="p-2 hover:bg-amber-500 rounded-xl border border-amber-600/20 hover:text-slate-950 transition-all text-gray-500" title="Scan QR Mesin">
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