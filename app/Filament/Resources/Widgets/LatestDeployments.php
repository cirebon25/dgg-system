<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Deployment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class LatestDeployments extends BaseWidget
{
    protected static ?string $heading = '📦 20 Penempatan Mesin Bulan Ini';
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Deployment::query()
                    ->latest()
                    ->with(['customer', 'machine', 'technician'])
                    ->whereExists(function ($q) {
                        $q->select(DB::raw(1))
                            ->from('service_logs')
                            ->where('tipe_kunjungan', 'RN')
                            ->whereNull('deleted_at')
                            ->whereColumn('service_logs.machine_id', 'deployments.machine_id')
                            ->whereColumn('service_logs.customer_id', 'deployments.customer_id')
                            ->whereNotExists(function ($q2) {
                                $q2->select(DB::raw(1))
                                    ->from('service_logs as sl2')
                                    ->where('sl2.tipe_kunjungan', 'RR')
                                    ->whereNull('sl2.deleted_at')
                                    ->whereColumn('sl2.machine_id', 'deployments.machine_id')
                                    ->whereColumn('sl2.customer_id', 'deployments.customer_id')
                                    ->whereColumn('sl2.tanggal', '>=', 'service_logs.tanggal');
                            });
                    })
                    ->whereMonth('tanggal_instal', now()->month)
                    ->whereYear('tanggal_instal', now()->year)
                    ->limit(20)
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Nama Customer')
                    ->icon('heroicon-m-user')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('Serial Number')
                    ->icon('heroicon-m-cpu-chip')
                    ->copyable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tanggal_instal')
                    ->label('Tanggal Pasang')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->badge()
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}