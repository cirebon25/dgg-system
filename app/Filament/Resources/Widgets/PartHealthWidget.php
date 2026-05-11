<?php

namespace App\Filament\Widgets;

use App\Models\MachinePartHealth;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PartHealthWidget extends BaseWidget
{
    // Label judul di Dashboard
    protected static ?string $heading = '🚨 Peringatan Umur Sparepart';

    // Urutan Widget (1 adalah paling atas)
    protected static ?int $sort = 3;

    // Lebar Widget (full agar enak dibaca)
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // // Mengambil data kesehatan part, urutkan dari pemakaian tertinggi
                // MachinePartHealth::query()->orderBy('current_usage', 'desc')
                \App\Models\MachinePartHealth::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('Serial Number')
                    ->description(fn (MachinePartHealth $record): string => $record->machine->customer->nama_customer ?? 'Lokasi tidak diketahui')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable(),

                Tables\Columns\TextColumn::make('current_usage')
                    ->label('Pemakaian')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80000 => 'danger',
                        $state >= 40000 => 'warning',
                        default => 'success', // Data 0 akan berwarna HIJAU
                    }),

                Tables\Columns\TextColumn::make('last_replaced_counter')
                    ->label('Counter Terakhir Ganti')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_replaced_at')
                    ->label('Tgl Ganti Terakhir')
                    ->date()
                    ->since() // Menampilkan "2 months ago", dsb.
                    ->color('gray'),
            ])
            ->actions([
                // Tambahkan tombol cepat untuk melihat detail mesin jika perlu
                Tables\Actions\Action::make('view_machine')
                    ->label('Cek Mesin')
                    ->icon('heroicon-m-magnifying-glass')
                    ->url(fn (MachinePartHealth $record): string => "/admin/machines/{$record->machine_id}/edit"),
            ]);
    }
}
