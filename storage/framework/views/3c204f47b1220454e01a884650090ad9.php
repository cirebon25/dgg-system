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

    <form wire:submit="submit">
        <?php echo e($this->form); ?>


        <div class="mt-6 flex justify-end">
            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['type' => 'submit','size' => 'lg','color' => 'warning','icon' => 'heroicon-o-arrow-path']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'lg','color' => 'warning','icon' => 'heroicon-o-arrow-path']); ?>
                PROSES TUKAR GULING SEKARANG
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
        </div>
    </form>

    
    <div class="mt-10">
        <h2 class="text-lg font-bold mb-4 text-gray-700 dark:text-gray-200">
            🔄 Riwayat Rolling Unit (20 Terakhir)
        </h2>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($riwayat) === 0): ?>
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border text-center text-gray-400 text-sm">
                Belum ada riwayat rolling.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto rounded-xl border shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 dark:bg-gray-1000 text-gray-600 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">SN Lama</th>
                            <th class="px-4 py-3">SN Baru</th>
                            <th class="px-4 py-3">Counter Akhir</th>
                            <th class="px-4 py-3">Teknisi</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php echo e(\Carbon\Carbon::parse($row->tanggal)->format('d/m/Y')); ?>

                                </td>
                                <td class="px-4 py-3 font-semibold"><?php echo e($row->nama_customer ?? '-'); ?></td>
                                <td class="px-4 py-3">
                                    <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                        <?php echo e($row->sn_lama ?? '-'); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded">
                                        <?php echo e($row->sn_baru ?? '-'); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    BW: <?php echo e(number_format($row->counter_bw_final ?? 0)); ?><br>
                                    CL: <?php echo e(number_format($row->counter_color_final ?? 0)); ?>

                                </td>
                                <td class="px-4 py-3"><?php echo e($row->nama_technician ?? '-'); ?></td>
                                <td class="px-4 py-3 text-gray-500 text-xs"><?php echo e($row->keterangan ?? '-'); ?></td>
                                <td class="px-4 py-3 text-center">
                                    
                                    <a href="<?php echo e(route('cetak.sj-rolling', $row->id)); ?>" target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition">
                                        🖨️ Cetak SJ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\dgg-system\resources\views/filament/pages/ganti-mesin.blade.php ENDPATH**/ ?>