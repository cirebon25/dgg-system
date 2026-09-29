<x-filament-panels::page>

    <div x-data="pusatCetak()" class="space-y-12">

        {{-- MODAL PILIH PERIODE --}}
        <div
            x-show="modalOpen"
            x-cloak
            x-transition.opacity
            @keydown.escape.window="tutup()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            role="dialog"
            aria-modal="true"
            :aria-label="modalTitle"
        >
            <div
                @click.outside="tutup()"
                x-transition
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4"
            >
                <h2 class="text-lg font-bold mb-4" x-text="modalTitle"></h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="pusat-cetak-bulan">Bulan</label>
                        <select
                            id="pusat-cetak-bulan"
                            x-model="selectedMonth"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm"
                        >
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="pusat-cetak-tahun">Tahun</label>
                        <select
                            id="pusat-cetak-tahun"
                            x-model="selectedYear"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm"
                        >
                            @for ($y = now()->year; $y >= 2024; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-5">
                    <button
                        type="button"
                        @click="tutup()"
                        class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="cetak()"
                        class="flex-1 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold hover:bg-blue-700"
                    >
                        🖨️ Cetak
                    </button>
                </div>
            </div>
        </div>

        {{-- KATEGORI LAPORAN --}}
        @foreach (\App\Support\PusatCetak\ReportCatalog::groups() as $kategori => $laporan)
            <section class="space-y-4">
                <h4 class="text-xl font-bold text-gray-800 dark:text-gray-100 pb-2 border-b-2 border-gray-200 dark:border-gray-700">
                    {{ $kategori }}
                </h4>

                {{-- Ditambahkan pb-4 dan space-y agar card ganjil tetap punya jarak aman ke bawah --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4">
                    @foreach ($laporan as $item)
                        <x-report-card
                            :icon="$item['icon']"
                            :title="$item['title']"
                            :description="$item['description']"
                            :color="$item['color']"
                            :button-icon="$item['buttonIcon']"
                            :button-label="$item['buttonLabel']"
                            :href="$item['href'] ?? null"
                            :url-template="$item['urlTemplate'] ?? null"
                        />
                    @endforeach
                </div>
            </section>
        @endforeach

    </div>

    @script
    <script>
        Alpine.data('pusatCetak', () => ({
            modalOpen: false,
            modalTitle: '',
            activeUrlTemplate: '',
            selectedMonth: String(new Date().getMonth() + 1).padStart(2, '0'),
            selectedYear: String(new Date().getFullYear()),

            buka(urlTemplate, judul) {
                this.activeUrlTemplate = urlTemplate;
                this.modalTitle = '🖨️ Cetak ' + judul;
                this.selectedMonth = String(new Date().getMonth() + 1).padStart(2, '0');
                this.selectedYear = String(new Date().getFullYear());
                this.modalOpen = true;
            },

            tutup() {
                this.modalOpen = false;
            },

            cetak() {
                if (! this.activeUrlTemplate) {
                    return;
                }

                const url = this.activeUrlTemplate
                    .replace('__B__', this.selectedMonth)
                    .replace('__T__', this.selectedYear);

                window.open(url, '_blank');
                this.tutup();
            },
        }));
    </script>
    @endscript

</x-filament-panels::page>