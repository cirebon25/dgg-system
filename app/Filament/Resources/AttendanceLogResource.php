<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceLogResource\Pages;
use App\Models\AttendanceLog;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'Kepegawaian';

    protected static ?string $navigationLabel = 'Log Absensi';

    protected static ?string $modelLabel = 'Log Absensi';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('employee'))
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Nama')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pin')
                    ->label('PIN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scan_at')
                    ->label('Waktu Scan')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('scan_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari')->label('Dari')->default(now()->startOfMonth()),
                        DatePicker::make('sampai')->label('Sampai')->default(now()),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn($q, $d) => $q->whereDate('scan_at', '>=', $d))
                        ->when($data['sampai'] ?? null, fn($q, $d) => $q->whereDate('scan_at', '<=', $d))),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceLogs::route('/'),
        ];
    }
}