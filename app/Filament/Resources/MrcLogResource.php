<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MrcLogResource\Pages;
use App\Models\ServiceLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MrcLogResource extends Resource
{
    protected static ?string $model = ServiceLog::class;
    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Rekap MRC';
    protected static ?string $navigationGroup = 'MRC & Billing';
    protected static ?string $slug            = 'mrc-log';
    protected static ?int    $navigationSort  = 2;

  public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('is_mrc', true)   
        ->with(['machine', 'customer', 'technician']);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('machine.tipe_model')
                    ->label('Model Mesin')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('counter_bw')
                    ->label('Counter BW')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage_bw')
                    ->label('Usage BW')
                    ->numeric()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('counter_color')
                    ->label('Counter Color')
                    ->numeric()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('usage_color')
                    ->label('Usage Color')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetakMrc')
                    ->label('Cetak Rekap MRC')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April',    '05' => 'Mei',      '06' => 'Juni',
                                '07' => 'Juli',     '08' => 'Agustus',  '09' => 'September',
                                '10' => 'Oktober',  '11' => 'November', '12' => 'Desember',
                            ])
                            ->required()
                            ->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('mrc.rekap', $data)),
            ])
            ->filters([
                Tables\Filters\Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April',    '05' => 'Mei',      '06' => 'Juni',
                                '07' => 'Juli',     '08' => 'Agustus',  '09' => 'September',
                                '10' => 'Oktober',  '11' => 'November', '12' => 'Desember',
                            ])
                            ->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->default(date('Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['month'], fn($q) => $q->whereMonth('tanggal', $data['month']))
                            ->when($data['year'],  fn($q) => $q->whereYear('tanggal',  $data['year']));
                    }),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return app(ServiceLogResource::class)->form($form);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMrcLogs::route('/'),
            'edit'  => Pages\EditMrcLog::route('/{record}/edit'),
        ];
    }
}