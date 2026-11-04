<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Log Inputan';
    protected static ?string $modelLabel = 'Log Inputan';
    protected static ?string $pluralModelLabel = 'Log Inputan';
    protected static ?string $navigationGroup = 'Pengaturan';

    // Nanti dibatasi sesuai role, misalnya:
    // public static function canViewAny(): bool
    // {
    //     return auth()->user()?->role === 'admin';
    // }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Modul')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn(?string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'created' => 'Tambah',
                        'updated' => 'Ubah',
                        'deleted' => 'Hapus',
                        default   => $state,
                    }),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID Data')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perubahan')
                    ->label('Perubahan')
                    ->state(function (Activity $record) {
                        $new = $record->properties['attributes'] ?? [];
                        $old = $record->properties['old'] ?? [];

                        return collect($new)
                            ->map(fn($v, $k) => $k . ': ' . (isset($old[$k]) ? json_encode($old[$k]) . ' → ' : '') . json_encode($v))
                            ->take(3)
                            ->implode(' | ');
                    })
                    ->limit(80)
                    ->tooltip(fn(Activity $record) => json_encode($record->properties, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Modul')
                    ->options(fn() => Activity::query()->distinct()->pluck('log_name', 'log_name')->all()),
                Tables\Filters\SelectFilter::make('event')
                    ->label('Aksi')
                    ->options([
                        'created' => 'Tambah',
                        'updated' => 'Ubah',
                        'deleted' => 'Hapus',
                    ]),
                Tables\Filters\SelectFilter::make('causer_id')
                    ->label('User')
                    ->options(fn() => \App\Models\User::pluck('name', 'id')->all()),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari'),
                        \Filament\Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(fn($query, array $data) => $query
                        ->when($data['dari'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['sampai'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Detail'),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('created_at')->label('Waktu')->dateTime('d/m/Y H:i:s'),
            TextEntry::make('causer.name')->label('User')->placeholder('-'),
            TextEntry::make('log_name')->label('Modul'),
            TextEntry::make('description')->label('Keterangan'),
            TextEntry::make('subject_id')->label('ID Data'),
            TextEntry::make('data_lama')
                ->label('Data Lama')
                ->state(fn(Activity $record) => json_encode($record->properties['old'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))
                ->columnSpanFull()
                ->extraAttributes(['style' => 'white-space: pre-wrap']),
            TextEntry::make('data_baru')
                ->label('Data Baru')
                ->state(fn(Activity $record) => json_encode($record->properties['attributes'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))
                ->columnSpanFull()
                ->extraAttributes(['style' => 'white-space: pre-wrap']),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
