<x-filament::page>
    <div class="space-y-6">
        {{-- FILTER --}}
        <x-filament::section>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Bulan</label>
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input.select wire:model.live="month">
                            @foreach ([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input.select wire:model.live="year">
                            @for ($y = now()->year + 1; $y >= now()->year - 4; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Teknisi</label>
                    <x-filament::input.wrapper class="mt-1">
                        <x-filament::input.select wire:model.live="technician_id">
                            <option value="">Semua Teknisi</option>
                            @foreach ($this->technicians as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>

        {{-- RINGKASAN --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-filament::section>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-arrow-down-circle" class="h-8 w-8 text-success-500" />
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Stok Masuk ke Gudang</p>
                        <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                            +{{ number_format($this->summary['masuk'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-400">Barang baru dari supplier + part yang diretur teknisi</p>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-arrow-right-circle" class="h-8 w-8 text-info-500" />
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sedang Dipinjam Teknisi</p>
                        <p class="text-2xl font-bold text-info-600 dark:text-info-400">
                            {{ number_format($this->summary['dipinjamkan'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-400">Masih milik perusahaan, belum tentu terpakai</p>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="h-8 w-8 text-danger-500" />
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Terpakai Permanen</p>
                        <p class="text-2xl font-bold text-danger-600 dark:text-danger-400">
                            -{{ number_format($this->summary['terpakai'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-400">Habis dipakai servis atau terpasang di mesin baru</p>
            </x-filament::section>
        </div>

        {{-- LEGENDA --}}
        <x-filament::section>
            <x-slot name="heading">Keterangan Jenis Transaksi</x-slot>
            <div class="flex flex-wrap gap-2">
                @foreach (['MASUK', 'PINJAM', 'PAKAI', 'DEPLOY', 'RETUR', 'ROLLING'] as $tipe)
                    @php($meta = $this->typeMeta($tipe))
                    <x-filament::badge :color="$meta['color']" :icon="$meta['icon']">
                        {{ $meta['label'] }}
                    </x-filament::badge>
                @endforeach
            </div>
        </x-filament::section>

        {{-- RIWAYAT PER TANGGAL --}}
        @forelse ($this->groupedHistory as $tanggal => $items)
            <x-filament::section>
                <x-slot name="heading">
                    {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </x-slot>

                <div class="divide-y divide-gray-100 dark:divide-white/10">
                    @foreach ($items as $item)
                        @php($meta = $this->typeMeta($item->tipe))
                        <div class="flex flex-col gap-2 py-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex items-start gap-3">
                                <x-filament::icon
                                    :icon="$meta['icon']"
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-{{ $meta['color'] }}-500"
                                />
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $item->part }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <x-filament::badge :color="$meta['color']" size="sm">
                                            {{ $meta['label'] }}
                                        </x-filament::badge>
                                        @if ($item->nama_teknisi && $item->nama_teknisi !== '-')
                                            <x-filament::badge color="gray" size="sm" icon="heroicon-o-user">
                                                {{ $item->nama_teknisi }}
                                            </x-filament::badge>
                                        @endif
                                    </div>
                                    @if ($item->keterangan)
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->keterangan }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right sm:min-w-[90px]">
                                <span @class([
                                    'text-lg font-bold',
                                    'text-success-600 dark:text-success-400' => $meta['sign'] === '+',
                                    'text-danger-600 dark:text-danger-400' => $meta['sign'] === '-',
                                    'text-gray-400' => $meta['sign'] === '',
                                ])>
                                    @if ($item->jumlah > 0)
                                        {{ $meta['sign'] }}{{ number_format($item->jumlah, 0, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="py-10 text-center text-gray-400">
                    <x-filament::icon icon="heroicon-o-inbox" class="mx-auto h-10 w-10" />
                    <p class="mt-2">Tidak ada mutasi sparepart pada periode yang dipilih.</p>
                </div>
            </x-filament::section>
        @endforelse

        {{-- MUAT LEBIH BANYAK --}}
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            Menampilkan {{ min($this->displayLimit, $this->summary['total_transaksi']) }}
            dari {{ $this->summary['total_transaksi'] }} transaksi
        </div>

        @if ($this->hasMore)
            <div class="text-center">
                <x-filament::button wire:click="loadMore" color="gray" icon="heroicon-o-arrow-down">
                    Muat {{ min(30, $this->historyData->count() - $this->displayLimit) }} Transaksi Lagi
                </x-filament::button>
            </div>
        @endif
    </div>
</x-filament::page>