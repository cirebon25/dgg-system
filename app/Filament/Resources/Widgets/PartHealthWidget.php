<?php

namespace App\Filament\Widgets;

use App\Models\MachinePartHealth;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PartHealthWidget extends BaseWidget
{
    // Label judul di Dashboard (Lebih Tegas dan Profesional)
    protected static ?string $heading = '🚨 Peringatan Kritis Umur & Pemakaian Sparepart';

    // Urutan Widget di Dashboard
    protected static ?int $sort = 3;

    // Lebar Widget Full Screen agar data tidak terpotong
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Eager loading relasi agar data langsung AKTIF 100% dan urutkan dari pemakaian tertinggi
                MachinePartHealth::query()
                    ->with(['machine.customer', 'sparepart'])
                    ->orderBy('current_usage', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('Serial Number Mesin')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable() // Admin bisa copy S/N sekali klik!
                    ->description(
                        fn(MachinePartHealth $record): string =>
                        $record->machine?->customer?->nama_customer ?? '📍 Lokasi/Customer Tidak Terdeteksi'
                    ),

                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable()
                    ->wrap() // Tulisan sparepart panjang otomatis turun ke bawah, tidak merusak layout
                    ->description(
                        fn(MachinePartHealth $record): string =>
                        'ID Part: #' . ($record->sparepart_id ?? '-')
                    ),

                Tables\Columns\TextColumn::make('current_usage')
                    ->label('Total Pemakaian')
                    ->numeric(decimalPlaces: 0, locale: 'id') // Format angka ribuan dengan titik ala Indonesia
                    ->alignCenter()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 80000 => 'danger',   // Pemakaian Kritis (MERAH)
                        $state >= 40000 => 'warning',  // Pemakaian Sedang (KUNING)
                        default => 'success',          // Pemakaian Aman (HIJAU)
                    }),

                Tables\Columns\TextColumn::make('last_replaced_counter')
                    ->label('Counter Ganti Terakhir')
                    ->numeric(decimalPlaces: 0, locale: 'id')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_replaced_at')
                    ->label('Waktu Penggantian Terakhir')
                    ->dateTime('d M Y') // Format tanggal rapi: contoh 21 Mei 2026
                    ->sortable()
                    ->description(
                        fn(MachinePartHealth $record): string =>
                        $record->last_replaced_at
                            ? $record->last_replaced_at->diffForHumans() // Tampilan "2 bulan yang lalu"
                            : 'Belum pernah diganti'
                    )
                    ->color('gray'),
            ])
            ->actions([
                // Tombol aksi elegan dengan warna warning (kuning) agar kontras untuk cek mesin terkait
                Tables\Actions\Action::make('view_machine')
                    ->label('Cek Unit Mesin')
                    ->icon('heroicon-m-magnifying-glass')
                    ->color('warning')
                    ->button() // Diubah jadi bentuk tombol solid agar terlihat sangat premium
                    ->url(fn(MachinePartHealth $record): string => "/admin/machines/{$record->machine_id}/edit"),
            ])
            ->emptyStateHeading('Semua Sparepart Aman!')
            ->emptyStateDescription('Tidak ada data pemakaian sparepart yang terdeteksi saat ini.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}
