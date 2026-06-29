<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'label',
    'results',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'label',
    'results',
]); ?>
<?php foreach (array_filter(([
    'label',
    'results',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<li
    <?php echo e($attributes->class(['fi-global-search-result-group'])); ?>

>
    <div
        class="sticky top-0 z-10 border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-gray-900"
    >
        <h3
            class="px-4 py-2 text-sm font-semibold capitalize text-gray-950 dark:bg-white/5 dark:text-white"
        >
            <?php echo e($label); ?>

        </h3>
    </div>

    <ul class="divide-y divide-gray-200 dark:divide-white/10">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal778cd2be112571395dc0ac2afc159ec1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal778cd2be112571395dc0ac2afc159ec1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.global-search.result','data' => ['actions' => $result->actions,'details' => $result->details,'title' => $result->title,'url' => $result->url]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament-panels::global-search.result'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($result->actions),'details' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($result->details),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($result->title),'url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($result->url)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal778cd2be112571395dc0ac2afc159ec1)): ?>
<?php $attributes = $__attributesOriginal778cd2be112571395dc0ac2afc159ec1; ?>
<?php unset($__attributesOriginal778cd2be112571395dc0ac2afc159ec1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal778cd2be112571395dc0ac2afc159ec1)): ?>
<?php $component = $__componentOriginal778cd2be112571395dc0ac2afc159ec1; ?>
<?php unset($__componentOriginal778cd2be112571395dc0ac2afc159ec1); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
</li>
<?php /**PATH C:\laragon\www\dgg-system\vendor\filament\filament\resources\views/components/global-search/result-group.blade.php ENDPATH**/ ?>