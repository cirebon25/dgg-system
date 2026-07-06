<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Grouping\Group;

class CustomerResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'admin_teknik',];

    protected static ?string $model = Customer::class;
    protected static ?string $navigationLabel = 'Customer';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;
    protected static bool $globallySearchable = true;

    protected static function technicianColor(?string $nama): string
    {
        return match (true) {
            str_contains(strtoupper($nama ?? ''), 'RUDI')   => 'danger',
            str_contains(strtoupper($nama ?? ''), 'IDRUS')  => 'warning',
            str_contains(strtoupper($nama ?? ''), 'SUKANA') => 'success',
            str_contains(strtoupper($nama ?? ''), 'GETAR')  => 'info',
            str_contains(strtoupper($nama ?? ''), 'AKHSAN') => 'primary',
            str_contains(strtoupper($nama ?? ''), 'ISMET')  => 'gray',
            str_contains(strtoupper($nama ?? ''), 'YUDI')   => 'warning',
            default                                          => 'gray',
        };
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_customer', 'kota', 'alamat'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->nama_customer;
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Kota'   => $record->kota,
            'Alamat' => $record->alamat ?? '-',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('rayon_id')
                ->relationship('rayon', 'nama_rayon')
                ->required()
                ->preload(),
            Forms\Components\TextInput::make('nama_customer')
                ->label('Nama Instansi / Perorangan')
                ->required(),
            Forms\Components\TextInput::make('kota')
                ->label('Kota / Kabupaten')
                ->required(),
            Forms\Components\Textarea::make('alamat')
                ->columnSpanFull(),
            Forms\Components\Select::make('technician_id')
                ->label('Teknisi Penanggung Jawab')
                ->relationship('technician', 'nama_technician')
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rayon.nama_rayon')
                    ->label('Rayon')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color(fn(?string $state): string => match (true) {
                        str_contains(strtoupper($state ?? ''), 'BARAT DAYA') => 'success',
                        str_contains(strtoupper($state ?? ''), 'BARAT')      => 'info',
                        str_contains(strtoupper($state ?? ''), 'UTARA')      => 'warning',
                        str_contains(strtoupper($state ?? ''), 'SELATAN')    => 'danger',
                        str_contains(strtoupper($state ?? ''), 'TIMUR')      => 'danger',
                        default                                               => 'gray',
                    }),

                TextColumn::make('nama_customer')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(fn(Customer $record): string => $record->alamat ?? '-')
                    ->wrap()
                    ->summarize(Count::make()->label('Total')),

                TextColumn::make('kota')
                    ->label('Kota')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->placeholder('— Belum Diset —')
                    ->badge()
                    ->searchable()
                    ->color(fn(?string $state): string => static::technicianColor($state)),

                TextColumn::make('deployments_count')
                    ->label('Unit')
                    ->counts('deployments')
                    ->suffix(' Unit')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state <= 3  => 'warning',
                        $state <= 10 => 'success',
                        default      => 'danger',
                    })
                    ->tooltip(fn(int $state): string => match (true) {
                        $state === 0 => 'Belum ada unit terpasang',
                        $state <= 3  => 'Unit sedikit',
                        $state <= 10 => 'Unit normal',
                        default      => 'Unit sangat banyak',
                    }),
            ])

            ->defaultGroup(
                Group::make('tech_kota_key')
                    ->getTitleFromRecordUsing(function ($record) {
                        $namaTek = $record->technician?->nama_technician ?? 'Tanpa Teknisi';
                        $kota    = $record->kota ?? 'Tanpa Kota';

                        $emoji = match (true) {
                            str_contains(strtoupper($namaTek), 'RUDI')   => '🔴',
                            str_contains(strtoupper($namaTek), 'IDRUS')  => '🟡',
                            str_contains(strtoupper($namaTek), 'SUKANA') => '🟢',
                            str_contains(strtoupper($namaTek), 'GETAR')  => '🔵',
                            str_contains(strtoupper($namaTek), 'AKHSAN') => '🟣',
                            str_contains(strtoupper($namaTek), 'ISMET')  => '⚫',
                            str_contains(strtoupper($namaTek), 'YUDI')   => '🟠',
                            default                                       => '⚪',
                        };

                        return "{$emoji} {$namaTek}   - {$kota}";
                    })
                    ->label('')
                    ->collapsible()
                    ->orderQueryUsing(
                        fn(Builder $query, string $direction) => $query
                            ->orderBy('technician_id', $direction)
                            ->orderBy('kota', $direction)
                    )
            )

            ->striped()

            ->filters([
                Tables\Filters\SelectFilter::make('rayon_id')
                    ->relationship('rayon', 'nama_rayon')
                    ->label('Filter Rayon'),
                Tables\Filters\SelectFilter::make('technician_id')
                    ->relationship('technician', 'nama_technician')
                    ->label('Filter Teknisi'),
                Tables\Filters\TrashedFilter::make(),
            ])

            ->actions([
                Tables\Actions\EditAction::make()->label('')->tooltip('Edit'),
                Tables\Actions\RestoreAction::make()->label('')->tooltip('Pulihkan'),
                Tables\Actions\ForceDeleteAction::make()->label('')->tooltip('Hapus Permanen'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    // ← OPTIMASI: Tambahkan with() untuk eager load rayon & technician
    // selectRaw yang sudah ada tetap dipertahankan persis seperti semula
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->selectRaw("*, CONCAT(COALESCE(technician_id, 0), '-', COALESCE(kota, '')) as tech_kota_key")
            ->with(['rayon', 'technician']); // ← OPTIMASI
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
