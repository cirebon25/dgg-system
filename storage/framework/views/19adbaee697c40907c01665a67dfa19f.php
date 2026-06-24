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

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="text-xs text-slate-400 mb-1 block">Bulan</label>
            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['wire:model.live' => 'month']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'month']); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(str_pad($m, 2, '0', STR_PAD_LEFT)); ?>">
                            <?php echo e(\Carbon\Carbon::create(null, $m)->translatedFormat('F')); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $attributes = $__attributesOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__attributesOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $component = $__componentOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__componentOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
        </div>
        <div>
            <label class="text-xs text-slate-400 mb-1 block">Tahun</label>
            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <?php if (isset($component)) { $__componentOriginal97dc683fe4ff7acce9e296503563dd85 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal97dc683fe4ff7acce9e296503563dd85 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.select','data' => ['wire:model.live' => 'year']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::input.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'year']); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = range(date('Y'), 2024); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $attributes = $__attributesOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__attributesOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal97dc683fe4ff7acce9e296503563dd85)): ?>
<?php $component = $__componentOriginal97dc683fe4ff7acce9e296503563dd85; ?>
<?php unset($__componentOriginal97dc683fe4ff7acce9e296503563dd85); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $attributes = $__attributesOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__attributesOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $component = $__componentOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__componentOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
        </div>

        
        <?php
            $totalMasuk = collect($historyData)
                ->whereIn('tipe', ['MASUK', 'RETUR'])
                ->sum('jumlah');
            $totalKeluar = collect($historyData)
                ->whereIn('tipe', ['PINJAM', 'PAKAI', 'DEPLOY'])
                ->sum('jumlah');
            $totalTrx = collect($historyData)->count();
        ?>
        <div class="col-span-2 flex gap-3 items-end">
            <div
                style="flex:1;background:#064e3b22;border:1px solid #065f46;border-radius:8px;padding:8px 16px;text-align:center">
                <div style="font-size:11px;color:#6ee7b7">Total Masuk</div>
                <div style="font-size:20px;font-weight:bold;color:#34d399">+<?php echo e(number_format($totalMasuk)); ?></div>
            </div>
            <div
                style="flex:1;background:#4c051922;border:1px solid #9f1239;border-radius:8px;padding:8px 16px;text-align:center">
                <div style="font-size:11px;color:#fca5a5">Total Keluar</div>
                <div style="font-size:20px;font-weight:bold;color:#f87171">-<?php echo e(number_format($totalKeluar)); ?></div>
            </div>
            <div
                style="flex:1;background:#1e293b;border:1px solid #553a33;border-radius:8px;padding:8px 16px;text-align:center">
                <div style="font-size:11px;color:#94a3b8">Total Transaksi</div>
                <div style="font-size:20px;font-weight:bold;color:#e2e8f0"><?php echo e($totalTrx); ?></div>
            </div>
        </div>
    </div>

    
    <div style="background:#191c22;border:1px solid #334155;border-radius:12px;padding:16px">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($historyData) || count($historyData) === 0): ?>
            <div style="text-align:center;padding:48px;color:#64748b">
                <p>Tidak ada data mutasi untuk periode ini.</p>
            </div>
        <?php else: ?>
            <table style="width:100%;border-collapse:collapse;color:#e2e8f0;font-size:13px">
                <thead>
                    <tr style="background:#0f172a;color:#94a3b8;font-size:11px;text-transform:uppercase">
                        <th style="padding:10px 12px;border-bottom:1px solid #554c33;text-align:left">Tanggal</th>
                        <th style="padding:10px 12px;border-bottom:1px solid #334155;text-align:left">Nama Sparepart
                        </th>
                        <th style="padding:10px 12px;border-bottom:1px solid #334155;text-align:left">Tipe</th>
                        <th style="padding:10px 12px;border-bottom:1px solid #334155;text-align:center">Jumlah</th>
                        <th style="padding:10px 12px;border-bottom:1px solid #553339;text-align:left">Detail Alur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $historyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $badgeStyle = match ($item->tipe) {
                                'MASUK' => 'background:#064e3b;color:#6ee7b7;border:1px solid #065f46',
                                'PINJAM' => 'background:#1e3a5f;color:#93c5fd;border:1px solid #1e40af',
                                'PAKAI' => 'background:#4c0519;color:#fca5a5;border:1px solid #9f1239',
                                'DEPLOY' => 'background:#431407;color:#fdba74;border:1px solid #9a3412',
                                'ROLLING' => 'background:#1e293b;color:#94a3b8;border:1px solid #475569',
                                'RETUR' => 'background:#422006;color:#fde68a;border:1px solid #92400e',
                                default => 'background:#1e293b;color:#94a3b8;border:1px solid #475569',
                            };
                            $isKeluar = in_array($item->tipe, ['PINJAM', 'PAKAI', 'DEPLOY']);
                            $isMasuk = in_array($item->tipe, ['MASUK', 'RETUR']);
                        ?>
                        <tr style="border-bottom:1px solid #1e293b" onmouseover="this.style.background='#0f172a'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:10px 12px;color:#dddee0">
                                <?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')); ?>

                            </td>
                            <td style="padding:10px 12px;font-weight:600">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->part === '-'): ?>
                                    <span style="color:#475569;font-style:italic">—</span>
                                <?php else: ?>
                                    <?php echo e($item->part); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:10px 12px">
                                <span
                                    style="<?php echo e($badgeStyle); ?>;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:bold">
                                    <?php echo e($item->tipe); ?>

                                </span>
                            </td>
                            <td style="padding:10px 12px;text-align:center;font-weight:bold">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->jumlah == 0): ?>
                                    <span style="color:#475569">—</span>
                                <?php elseif($isKeluar): ?>
                                    <span style="color:#f87171">-<?php echo e($item->jumlah); ?></span>
                                <?php elseif($isMasuk): ?>
                                    <span style="color:#34d399">+<?php echo e($item->jumlah); ?></span>
                                <?php else: ?>
                                    <span style="color:#94a3b8"><?php echo e($item->jumlah); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:10px 12px;color:#d2dae6;font-size:12px"><?php echo e($item->detail); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
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
<?php /**PATH C:\laragon\www\dgg-system\resources\views/filament/pages/sparepart-history.blade.php ENDPATH**/ ?>