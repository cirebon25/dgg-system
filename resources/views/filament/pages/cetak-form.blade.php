<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($forms as $form)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="bg-primary-100 dark:bg-primary-900 p-3 rounded-lg">
                        <x-heroicon-o-document-text class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $form->judul }}</p>
                        @if ($form->deskripsi)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $form->deskripsi }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2 mt-auto">
                    <a
                        href="{{ Storage::url($form->file_path) }}"
                        target="_blank"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition"
                    >
                        <x-heroicon-m-eye class="w-4 h-4" />
                        Lihat
                    </a>
                    <a
                        href="{{ Storage::url($form->file_path) }}"
                        download
                        class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition"
                    >
                        <x-heroicon-m-arrow-down-tray class="w-4 h-4" />
                        Unduh
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 dark:text-gray-400 py-12">
                <x-heroicon-o-document-text class="w-12 h-12 mx-auto mb-3 opacity-30" />
                <p>Belum ada form yang tersedia.</p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>