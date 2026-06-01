<?php

namespace App\Filament\Resources\Widgets;

use App\Models\MachinePartHealth;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PartHealthWidget extends BaseWidget
{
    protected static ?string $heading = '📋 Detail Kondisi & Pemakaian Seluruh Sparepart';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
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
                    ->copyable()
                    ->description(fn($record) => $record->machine?->customer?->nama_customer ?? '📍 Lokasi Tidak Terdeteksi'),

                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn($record) => 'ID Part: #' . ($record->sparepart_id ?? '-')),

                Tables\Columns\TextColumn::make('current_usage')
                    ->label('Total Pemakaian')
                    ->numeric(0, 'id')
                    ->alignCenter()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 80000 => 'danger',
                        $state >= 40000 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('last_replaced_at')
                    ->label('Pergantian Terakhir')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->description(fn($record) => $record->last_replaced_at ? $record->last_replaced_at->diffForHumans() : 'Belum pernah diganti')
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_machine')
                    ->label('Cek Unit')
                    ->icon('heroicon-m-magnifying-glass')
                    ->color('primary')
                    ->button()
                    ->url(fn(MachinePartHealth $record): string => "/admin/machines/{$record->machine_id}/edit"),
            ])
            ->emptyStateHeading('Semua Sparepart Aman!')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->paginated([5, 10]);
    }
}
