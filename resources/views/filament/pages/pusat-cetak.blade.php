<x-filament-panels::page>

    <style>
        .pc-wrap {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        /* ---------- Toolbar: cari + periode ---------- */
        .pc-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
            justify-content: space-between;
        }

        .pc-search {
            position: relative;
            flex: 1 1 280px;
            max-width: 420px;
        }

        .pc-search svg {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.1rem;
            height: 1.1rem;
            opacity: .5;
        }

        .pc-search input {
            width: 100%;
            padding: .7rem 1rem .7rem 2.6rem;
            border-radius: 999px;
            border: 1px solid rgba(128, 128, 128, .25);
            background: rgba(128, 128, 128, .08);
            color: inherit;
            font-size: .9rem;
            outline: none;
            transition: .2s;
        }

        .pc-search input:focus {
            border-color: rgb(var(--primary-500));
            box-shadow: 0 0 0 3px rgba(var(--primary-500), .2);
        }

        .pc-period {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem .5rem .35rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(128, 128, 128, .25);
            background: rgba(128, 128, 128, .08);
        }

        .pc-period svg {
            width: 1.1rem;
            height: 1.1rem;
            opacity: .6;
        }

        .pc-period span {
            font-size: .75rem;
            opacity: .6;
            margin-right: .25rem;
        }

        .pc-period select,
        .pc-period input {
            border: 0;
            background: rgba(128, 128, 128, .15);
            color: inherit;
            border-radius: 999px;
            padding: .4rem .9rem;
            font-size: .85rem;
            font-weight: 600;
            outline: none;
        }

        .pc-period input {
            width: 5.5rem;
        }

        .pc-period select option {
            color: #111;
        }

        /* ---------- Grup ---------- */
        .pc-group-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: .9rem;
        }

        .pc-group-icon {
            width: 2.4rem;
            height: 2.4rem;
            border-radius: .8rem;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgb(var(--c)), rgba(var(--c), .6));
            color: #fff;
            box-shadow: 0 6px 16px -6px rgb(var(--c));
        }

        .pc-group-icon svg {
            width: 1.3rem;
            height: 1.3rem;
        }

        .pc-group-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .pc-group-count {
            font-size: .72rem;
            padding: .15rem .6rem;
            border-radius: 999px;
            background: rgba(var(--c), .15);
            color: rgb(var(--c));
            font-weight: 600;
        }

        /* ---------- Kartu ---------- */
        .pc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: .85rem;
        }

        .pc-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .9rem 1rem;
            border-radius: 1rem;
            text-decoration: none;
            color: inherit;
            overflow: hidden;
            border: 1px solid rgba(128, 128, 128, .2);
            background: rgba(128, 128, 128, .06);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
        }

        .pc-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: rgb(var(--c));
            opacity: 0;
            transition: .2s;
        }

        .pc-card:hover {
            transform: translateY(-3px);
            border-color: rgba(var(--c), .6);
            background: rgba(var(--c), .08);
            box-shadow: 0 12px 24px -12px rgba(var(--c), .55);
        }

        .pc-card:hover::before {
            opacity: 1;
        }

        /* ---------- Kartu penting (highlight) ---------- */
        .pc-card.pc-highlight {
            background: linear-gradient(135deg, rgb(var(--c) / .16), rgb(var(--c) / .04));
            border-color: rgb(var(--c) / .5);
            box-shadow: 0 0 0 1px rgb(var(--c) / .12),
                0 10px 26px -16px rgb(var(--c));
        }

        /* garis aksen kiri selalu tampil */
        .pc-card.pc-highlight::before {
            opacity: 1;
        }

        .pc-card.pc-highlight:hover {
            background: linear-gradient(135deg, rgb(var(--c) / .24), rgb(var(--c) / .08));
            border-color: rgb(var(--c) / .8);
        }

        .pc-star {
            position: absolute;
            top: .4rem;
            right: .5rem;
            width: 1rem;
            height: 1rem;
            color: rgb(255 255 255 / .85);
            filter: drop-shadow(0 0 4px rgb(var(--c) / .8));
        }

        .pc-star svg {
            width: 100%;
            height: 100%;
        }

        .pc-ico {
            flex: none;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: .75rem;
            display: grid;
            place-items: center;
            background: rgba(var(--c), .15);
            color: rgb(var(--c));
            transition: .2s;
        }

        .pc-ico svg {
            width: 1.3rem;
            height: 1.3rem;
        }

        .pc-card:hover .pc-ico {
            background: rgb(var(--c));
            color: #fff;
        }

        .pc-text {
            min-width: 0;
            flex: 1;
        }

        .pc-label {
            font-size: .9rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .pc-desc {
            font-size: .75rem;
            opacity: .6;
            margin-top: .15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pc-badge {
            display: inline-block;
            margin-top: .3rem;
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            padding: .1rem .5rem;
            border-radius: 999px;
            background: rgba(245, 158, 11, .18);
            color: rgb(245 158 11);
        }

        .pc-arrow {
            flex: none;
            width: 1.1rem;
            height: 1.1rem;
            opacity: 0;
            transform: translateX(-6px);
            transition: .2s;
            color: rgb(var(--c));
        }

        .pc-card:hover .pc-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .pc-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            opacity: .6;
        }
    </style>

    <div class="pc-wrap" x-data="{ q: '' }">

        {{-- Toolbar --}}
        <div class="pc-toolbar">
            <div class="pc-search">
                <x-filament::icon icon="heroicon-o-magnifying-glass" />
                <input type="text" x-model="q" placeholder="Cari laporan… (mis. stok, mrc, kas)">
            </div>

            <div class="pc-period">
                <x-filament::icon icon="heroicon-o-calendar-days" />
                <span>Periode</span>
                <select wire:model.live="bulan">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <input type="number" wire:model.live.debounce.500ms="tahun">
            </div>
        </div>

        {{-- Grup --}}
        @foreach ($this->getDaftarCetak() as $grup)
            <section style="--c: {{ $grup['warna'] }}" x-data="{ get kosong() { return q !== '' && ![...$el.querySelectorAll('[data-label]')].some(e => e.dataset.label.includes(q.toLowerCase())) } }" x-show="! kosong">
                <div class="pc-group-head">
                    <div class="pc-group-icon"><x-filament::icon :icon="$grup['icon']" /></div>
                    <div class="pc-group-title">{{ $grup['judul'] }}</div>
                    <span class="pc-group-count">{{ count($grup['items']) }} laporan</span>
                </div>

                <div class="pc-grid">
                    @foreach ($grup['items'] as $item)
                        <a href="{{ $this->urlCetak($item) }}" target="_blank"
                            class="pc-card {{ !empty($item['highlight']) ? 'pc-highlight' : '' }}"
                            data-label="{{ strtolower($item['label'] . ' ' . ($item['desc'] ?? '') . ' ' . $grup['judul']) }}"
                            x-show="q === '' || $el.dataset.label.includes(q.toLowerCase())">

                            @if (!empty($item['highlight']))
                                <span class="pc-star" title="Laporan utama">
                                    <x-filament::icon icon="heroicon-s-star" />
                                </span>
                            @endif

                            <div class="pc-ico"><x-filament::icon :icon="$item['icon'] ?? 'heroicon-o-printer'" /></div>

                            <div class="pc-text">
                                <div class="pc-label">{{ $item['label'] }}</div>
                                @if (!empty($item['desc']))
                                    <div class="pc-desc">{{ $item['desc'] }}</div>
                                @endif
                                @if ($item['filter'] ?? false)
                                    <span class="pc-badge">
                                        {{ \Carbon\Carbon::create(null, $this->bulan, 1)->locale('id')->translatedFormat('M') }}
                                        {{ $this->tahun }}
                                    </span>
                                @endif
                            </div>

                            <x-filament::icon icon="heroicon-m-arrow-up-right" class="pc-arrow" />
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach

    </div>
</x-filament-panels::page>
