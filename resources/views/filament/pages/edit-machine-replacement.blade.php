<x-filament-panels::page>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div
            class="flex items-center justify-between bg-gradient-to-r from-amber-600 to-amber-700 text-white p-6 rounded-lg shadow-lg">
            <div>
                <h1 class="text-3xl font-bold">✏️ Edit Riwayat Rolling Unit</h1>
                <p class="text-amber-100 mt-2">
                    Ubah data counter atau informasi rolling jika ada kesalahan input
                </p>
            </div>
            <a href="{{ route('filament.admin.pages.ganti-mesin') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white text-amber-600 rounded-lg hover:bg-gray-100 font-bold transition">
                ← Kembali
            </a>
        </div>

        {{-- INFO SINGKAT --}}
        @if ($this->replacement)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- MESIN LAMA --}}
                <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded">
                    <h3 class="font-bold text-red-700 text-lg mb-2">🔴 MESIN DITARIK (LAMA)</h3>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p><span class="font-semibold">SN:</span> {{ $this->replacement->oldMachine?->serial_number }}
                        </p>
                        <p><span class="font-semibold">Customer:</span>
                            {{ $this->replacement->customer?->nama_customer }}</p>
                        <p><span class="font-semibold">Tanggal Tarik:</span>
                            {{ \Carbon\Carbon::parse($this->replacement->tanggal)->format('d/m/Y') }}</p>
                        <div class="mt-2 pt-2 border-t border-red-200">
                            <p class="text-xs font-bold text-red-600">Counter Akhir:</p>
                            <p class="text-xs">BW: {{ number_format($this->replacement->counter_bw_final ?? 0) }}</p>
                            <p class="text-xs">Color: {{ number_format($this->replacement->counter_color_final ?? 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- MESIN BARU --}}
                <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded">
                    <h3 class="font-bold text-green-700 text-lg mb-2">🟢 MESIN PENGGANTI (BARU)</h3>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p><span class="font-semibold">SN:</span> {{ $this->replacement->newMachine?->serial_number }}
                        </p>
                        <p><span class="font-semibold">Status:</span> <span class="badge badge-success">Aktif</span></p>
                        <p><span class="font-semibold">Teknisi:</span>
                            {{ $this->replacement->technician?->nama_technician }}</p>
                        <div class="mt-2 pt-2 border-t border-green-200">
                            <p class="text-xs font-bold text-green-600">Counter Awal:</p>
                            <p class="text-xs">BW: {{ number_format($this->replacement->newMachine?->counter_bw ?? 0) }}
                            </p>
                            <p class="text-xs">Color:
                                {{ number_format($this->replacement->newMachine?->counter_color ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- FORM EDIT --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form wire:submit.prevent="save" class="space-y-6">
                {{ $this->form }}

                {{-- BUTTONS --}}
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('filament.admin.pages.ganti-mesin') }}"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 font-bold transition">
                        ✕ Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-lg transition">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
