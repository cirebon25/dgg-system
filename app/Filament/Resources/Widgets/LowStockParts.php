<?php

namespace App\Filament\Widgets;

use App\Models\Sparepart;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockParts extends BaseWidget
{
    // 1. JUDUL DENGAN IKON
    protected static ?string $heading = '⚠️ Peringatan Sparepart Kritis (Stok < 5)';

    // 2. URUTAN (Dibawah StockAlert/Statistik)
    protected static ?int $sort = 2;

    // 3. FITUR ANTI-GLITCH
    protected static bool $isLazy = true;

    // 4. LEBAR SETENGAH LAYAR
    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Filter barang yang stoknya kritis
                Sparepart::query()->where('stok', '<', 5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_sparepart')
                    ->label('Nama Barang')
                    ->weight('bold') // Biar lebih kebaca
                    ->searchable(),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Sisa')
                    ->badge()
                    ->color('danger')
                    ->suffix(' Unit')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('code_part')
                    ->label('Kode Part')
                    ->copyable() // Bonus: Biar Boss bisa klik & copy kode part-nya
                    ->color('gray'),
            ])
            // Menambahkan pagination sederhana biar tidak kepanjangan tabelnya
            ->paginated([5, 10]);
    }
}
