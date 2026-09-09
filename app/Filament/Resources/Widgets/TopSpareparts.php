<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Sparepart;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopSpareparts extends BaseWidget
{
    protected static ?string $heading = '📊 Top Sparepart Terlaris (Keluar Berdasarkan Servis)';
    protected static bool $isLazy = true;
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        // Ambil nilai filter yang sedang dipilih di UI widget
        $bulan = $this->tableFilters['bulan']['value'] ?? date('n');
        $tahun = $this->tableFilters['tahun']['value'] ?? date('Y');

        return $table
            ->query(
                Sparepart::query()
                    ->select('spareparts.*', DB::raw('COALESCE(SUM(service_log_spareparts.jumlah), 0) as total_keluar'))
                    ->leftJoin('service_log_spareparts', function ($join) use ($bulan, $tahun) {
                        $join->on('spareparts.id', '=', 'service_log_spareparts.sparepart_id');

                        if (!empty($bulan)) {
                            $join->whereMonth('service_log_spareparts.created_at', $bulan);
                        }
                        if (!empty($tahun)) {
                            $join->whereYear('service_log_spareparts.created_at', $tahun);
                        }
                    })
                    ->groupBy('spareparts.id')
                    ->orderByDesc('total_keluar')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->weight('semibold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_keluar')
                    ->label('Total Keluar (Bulan Ini)')
                    ->badge()
                    ->color('warning')
                    ->suffix(' Pcs')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Sisa Stok Gudang')
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->default(date('n'))
                    ->query(fn(Builder $query) => $query), // Mencegah error pencarian kolom di tabel spareparts

                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                    ->default(date('Y'))
                    ->query(fn(Builder $query) => $query), // Mencegah error pencarian kolom di tabel spareparts
            ])
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum Ada Data Suku Cadang')
            ->emptyStateDescription('Data sparepart terlaris akan muncul otomatis setelah ada transaksi penggunaan pada service log di bulan tersebut.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }
}
