<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    
    <div class="flex flex-wrap items-end gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
            <select wire:model.live="month"
                class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:ring-amber-500 focus:border-amber-500">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
                    '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
                    '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if($month == $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
            <select wire:model.live="year"
                class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:ring-amber-500 focus:border-amber-500">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = range(date('Y'), 2023); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($y); ?>" <?php if($year == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>

        <div class="text-xs text-gray-400 pb-1">
            Menampilkan data periode
            <b class="text-amber-600"><?php echo e($this->getNamaBulan($month)); ?> <?php echo e($year); ?></b>
        </div>
    </div>

    
    <div id="print-area"
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

        
        <div class="print-header px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700">
            <div class="text-center space-y-0.5">
                <h1 class="text-base font-black text-gray-900 dark:text-white tracking-wide uppercase">
                    Laporan Ranking Pemakaian Mesin
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    PT Dinamika Global Gemilang — DGG System
                </p>
                <p class="text-xs font-semibold text-amber-600">
                    Periode: <?php echo e($this->getNamaBulan($month)); ?> <?php echo e($year); ?>

                </p>
            </div>
        </div>

        
        <div class="overflow-x-auto">
            <?php $data = $this->getUsageData(); ?>

            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-900 dark:bg-gray-950 text-white">
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700">No.</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700">Customer</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700">Rayon</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700">Teknisi</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700">Serial Number</th>
                        <th class="px-3 py-2.5 text-left font-bold border border-gray-700">Tipe</th>
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700">Tgl Instal</th>
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700">Lama (Bln)</th>
                        <th class="px-3 py-2.5 text-right font-bold border border-gray-700">BW</th>
                        <th class="px-3 py-2.5 text-right font-bold border border-gray-700">Color</th>
                        <th class="px-3 py-2.5 text-right font-bold border border-gray-700">Total Bulan</th>
                        <th class="px-3 py-2.5 text-right font-bold border border-gray-700">Lifetime</th>
                        <th class="px-3 py-2.5 text-right font-bold border border-gray-700">Rata-rata/Bln</th>
                        <th class="px-3 py-2.5 text-center font-bold border border-gray-700">Kunjungan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900'); ?> hover:bg-amber-50 dark:hover:bg-amber-950/20 transition-colors">

                            <td class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 font-semibold text-gray-400">
                                <?php echo e($index + 1); ?>

                            </td>

                            <td class="px-3 py-2 border border-gray-100 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-200">
                                <?php echo e($row->nama_customer); ?>

                            </td>

                            <td class="px-3 py-2 border border-gray-100 dark:border-gray-700">
                                <?php
                                    $rayon = strtoupper($row->nama_rayon);
                                    $rayonClass = match(true) {
                                        str_contains($rayon, 'UTARA')     => 'bg-green-100 text-green-700',
                                        str_contains($rayon, 'SELATAN')   => 'bg-red-100 text-red-700',
                                        str_contains($rayon, 'BARAT DAYA')=> 'bg-yellow-100 text-yellow-700',
                                        str_contains($rayon, 'BARAT')     => 'bg-blue-100 text-blue-700',
                                        default                            => 'bg-gray-100 text-gray-600',
                                    };
                                ?>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold <?php echo e($rayonClass); ?>">
                                    <?php echo e($row->nama_rayon); ?>

                                </span>
                            </td>

                            <td class="px-3 py-2 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <?php echo e($row->nama_technician ?? '-'); ?>

                            </td>

                            <td class="px-3 py-2 border border-gray-100 dark:border-gray-700 font-mono text-gray-700 dark:text-gray-300">
                                <?php echo e($row->serial_number); ?>

                            </td>

                            <td class="px-3 py-2 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <?php echo e($row->tipe_model); ?>

                            </td>

                            <td class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <?php echo e(\Carbon\Carbon::parse($row->tanggal_instal)->format('d/m/Y')); ?>

                            </td>

                            <td class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <?php echo e($row->lama_pasang); ?>

                            </td>

                            <td class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono font-semibold text-gray-800 dark:text-gray-200">
                                <?php echo e(number_format($row->total_bw)); ?>

                            </td>

                            <td class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono font-semibold text-blue-600 dark:text-blue-400">
                                <?php echo e(number_format($row->total_color)); ?>

                            </td>

                            
                            <td class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono font-bold
                                <?php echo e($index === 0 ? 'text-white bg-amber-500' : 'text-amber-600'); ?>">
                                <?php echo e(number_format($row->total_bulan)); ?>

                            </td>

                            <td class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono text-gray-600 dark:text-gray-400">
                                <?php echo e(number_format($row->total_hidup)); ?>

                            </td>

                            <td class="px-3 py-2 text-right border border-gray-100 dark:border-gray-700 font-mono text-gray-600 dark:text-gray-400">
                                <?php echo e(number_format($row->rata_rata)); ?>

                            </td>

                            <td class="px-3 py-2 text-center border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                <?php echo e($row->total_kunjungan); ?>x
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="14" class="px-6 py-10 text-center text-gray-400 text-sm">
                                Tidak ada data untuk periode ini.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data->count() > 0): ?>
                    <tfoot>
                        <tr class="bg-amber-50 dark:bg-amber-950/30 font-bold border-t-2 border-amber-300">
                            <td colspan="8"
                                class="px-3 py-2.5 text-right text-xs border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                                TOTAL KESELURUHAN
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                                <?php echo e(number_format($data->sum('total_bw'))); ?>

                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-blue-600">
                                <?php echo e(number_format($data->sum('total_color'))); ?>

                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-amber-600">
                                <?php echo e(number_format($data->sum('total_bulan'))); ?>

                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-gray-600">
                                <?php echo e(number_format($data->sum('total_hidup'))); ?>

                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-xs border border-gray-200 dark:border-gray-700 text-gray-600">
                                -
                            </td>
                            <td class="px-3 py-2.5 text-center text-xs border border-gray-200 dark:border-gray-700 text-gray-600">
                                <?php echo e(number_format($data->sum('total_kunjungan'))); ?>x
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div>

        
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <p class="text-[10px] text-gray-400">Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?> WIB</p>
            <p class="text-[10px] text-gray-400">DGG System v3.0 — PT Dinamika Global Gemilang</p>
        </div>
    </div>

    
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-area,
            #print-area * { visibility: visible; }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print-header { display: block !important; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>

    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('printPage', () => {
                window.print();
            });
        });
    </script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\dgg-system\resources\views/filament/pages/rekap-usage.blade.php ENDPATH**/ ?>