<x-filament-panels::page>

    <div class="space-y-12" x-data="pusatCetak">

        {{-- MODAL PILIH PERIODE --}}
        <div x-show="modalOpen" x-cloak x-transition.opacity @keydown.escape.window="tutup()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" role="dialog" aria-modal="true"
            :aria-label="modalTitle">
            <div @click.outside="tutup()" x-transition
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm mx-4"
                style="padding: 1.5rem;">
                <h2 class="text-lg font-bold" style="margin-bottom: 1rem;" x-text="modalTitle"></h2>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label class="block text-sm font-medium" style="margin-bottom: 0.25rem;"
                            for="pusat-cetak-bulan">Bulan</label>
                        <x-filament::input.wrapper>
                            <x-filament::input.select id="pusat-cetak-bulan" x-model="selectedMonth">
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
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div>
                        <label class="block text-sm font-medium" style="margin-bottom: 0.25rem;"
                            for="pusat-cetak-tahun">Tahun</label>
                        <x-filament::input.wrapper>
                            <x-filament::input.select id="pusat-cetak-tahun" x-model="selectedYear">
                                @for ($y = now()->year; $y >= 2024; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <x-filament::button type="button" color="gray" x-on:click="tutup()">
                        Batal
                    </x-filament::button>

                    <x-filament::button type="button" color="primary" icon="heroicon-m-printer"
                        x-on:click="cetak()">
                        Cetak
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- KATEGORI LAPORAN --}}
        @foreach (\App\Support\PusatCetak\ReportCatalog::groups() as $kategori => $laporan)
            <section class="space-y-4">
                <h4
                    class="text-xl font-bold text-gray-800 dark:text-gray-100 pb-2 border-b-2 border-gray-200 dark:border-gray-700">
                    {{ $kategori }}
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4">
                    @foreach ($laporan as $item)
                        <x-report-card :icon="$item['icon']" :title="$item['title']" :description="$item['description']" :color="$item['color']"
                            :button-icon="$item['buttonIcon']" :button-label="$item['buttonLabel']" :href="$item['href'] ?? null" :url-template="$item['urlTemplate'] ?? null" />
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
                    if (!this.activeUrlTemplate) {
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