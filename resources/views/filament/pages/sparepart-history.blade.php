<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-4 mb-4">
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="month">
                @foreach(range(1,12) as $m)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>

    <div class="bg-slate-800 p-4 rounded-xl shadow-lg border border-slate-700">
        <table class="w-full text-left border-collapse text-slate-200">
            <thead>
                <tr class="bg-slate-900 text-slate-400 uppercase text-xs">
                    <th class="p-3 border-b border-slate-700">Tanggal</th>
                    <th class="p-3 border-b border-slate-700">Nama Sparepart</th>
                    <th class="p-3 border-b border-slate-700">Tipe</th>
                    <th class="p-3 border-b border-slate-700">Jumlah</th>
                    <th class="p-3 border-b border-slate-700">Detail Alur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historyData as $item)
                    <tr class="border-b border-slate-700 hover:bg-slate-700/50 transition-colors">
                        <td class="p-3">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                        <td class="p-3 font-medium">{{ $item->part }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs font-bold 
                                {{ $item->tipe == 'MASUK' ? 'bg-emerald-900 text-emerald-300' : 
                                   ($item->tipe == 'PINJAM' ? 'bg-amber-900 text-amber-300' : 'bg-rose-900 text-rose-300') }}">
                                {{ $item->tipe }}
                            </span>
                        </td>
                        <td class="p-3 font-bold text-white">{{ $item->jumlah }}</td>
                        <td class="p-3 text-slate-400 text-sm">{{ $item->detail }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>