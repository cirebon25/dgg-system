<x-filament-panels::page>
    <div class="pc-wrap" x-data="{ q: '' }">

        <div class="pc-toolbar">
            <div class="pc-search">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="m21 21-4.35-4.35" stroke-linecap="round" />
                </svg>
                <input type="search" x-model="q" placeholder="Cari form cetak..." aria-label="Cari form cetak">
            </div>
        </div>

        <section>
            <div class="pc-group-head" style="--c: 139 92 246;">
                <div class="pc-group-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" />
                    </svg>
                </div>
                <span class="pc-group-title">Form Cetak</span>
                <span class="pc-group-count">{{ count($forms) }} form</span>
            </div>

            <div class="pc-grid">
                @forelse ($forms as $form)
                    <article class="pc-card" style="--c: 139 92 246;" data-search="{{ $form['search'] }}"
                        x-show="$el.dataset.search.includes(q.trim().toLowerCase())">
                        @if ($form['url'])
                            <a href="{{ $form['url'] }}" class="pc-main" target="_blank" rel="noopener">
                                <div class="pc-ico">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <path d="M14 2v6h6" />
                                    </svg>
                                </div>
                                <div class="pc-text">
                                    <div class="pc-label">{{ $form['nama'] }}</div>
                                    <div class="pc-desc">
                                        {{ $form['keterangan'] !== '' ? $form['keterangan'] : 'Tanpa keterangan' }}
                                    </div>
                                </div>
                            </a>

                            <a href="{{ $form['url'] }}" class="pc-dl" download title="Unduh {{ $form['nama'] }}"
                                aria-label="Unduh {{ $form['nama'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
                                </svg>
                            </a>
                        @else
                            <div class="pc-main">
                                <div class="pc-ico">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <path d="M14 2v6h6" />
                                    </svg>
                                </div>
                                <div class="pc-text">
                                    <div class="pc-label">{{ $form['nama'] }}</div>
                                    <div class="pc-desc">File belum tersedia</div>
                                </div>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="pc-empty">Belum ada form cetak yang tersedia.</div>
                @endforelse
            </div>
        </section>

    </div>
</x-filament-panels::page>
