@props([
    'icon',
    'title',
    'description',
    'color' => 'primary',
    'buttonIcon' => 'heroicon-m-printer',
    'buttonLabel' => 'Cetak',
    'href' => null,
    'urlTemplate' => null,
])

<div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
    <h3 class="text-lg font-bold mb-2">{{ $icon }} {{ $title }}</h3>
    <p class="text-sm text-gray-500 mb-4">{{ $description }}</p>

    @if ($href)
        <x-filament::button tag="a" :href="$href" target="_blank" :icon="$buttonIcon" :color="$color">
            {{ $buttonLabel }}
        </x-filament::button>
    @else
        <x-filament::button type="button" :data-url="$urlTemplate" :data-title="$title"
            x-on:click="buka($el.dataset.url, $el.dataset.title)" :icon="$buttonIcon" :color="$color">
            {{ $buttonLabel }}
        </x-filament::button>
    @endif
</div>
