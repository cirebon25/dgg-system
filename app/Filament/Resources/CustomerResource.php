<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\SoftDeletingScope; 
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\DB;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationLabel = 'Customer';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

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
                ->preload(),
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
                    ->searchable(),
                TextColumn::make('nama_customer')
                    ->label('Pelanggan / Alamat')
                    ->searchable()
                    ->description(fn(Customer $record): string => $record->alamat ?? '-')
                    ->summarize(Count::make()->label('Total Pelanggan')),
                TextColumn::make('kota')
                    ->label('Kota')
                    ->searchable(),
                TextColumn::make('deployments_count')
                    ->label('Unit Terpasang')
                    ->counts('deployments')
                    ->suffix(' Unit')
                    ->badge()
                    ->color(fn(int $state): string => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi Utama')
                    ->placeholder('Belum Diset') 
                    ->badge()
                    ->color('info'),
            ])
            /* |--------------------------------------------------------------------------
            | 🌟 DESAIN PREMIUM GRUP WILAYAH KERJA (BERSIH & PRESISI)
            |--------------------------------------------------------------------------
            */
            ->defaultGroup(
                Group::make('nested_group_key')
                    ->label('Grup Wilayah')
                    ->getTitleFromRecordUsing(function (Customer $record) {
                        $namaTeknisi = strtoupper($record->technician?->nama_technician ?? 'TANPA TEKNISI');
                        $namaKota = strtoupper($record->kota ?? 'WILAYAH UMUM');
                        
                        // Hasil Tampilan di Layar:  👤 TEKNISI: IDRUS   •   📍 KOTA: CIREBON
                        return "👤 TEKNISI: {$namaTeknisi}   •   📍 KOTA: {$namaKota}";
                    })
                    ->collapsible()
            )
            ->filters([
                Tables\Filters\SelectFilter::make('rayon_id')
                    ->relationship('rayon', 'nama_rayon')
                    ->label('Filter Rayon'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(), 
                ]),
            ]);
    }

    /* |--------------------------------------------------------------------------
    | 🌟 REKAYASA SUBQUERY: SOLUSI MUTLAK AMBIGUOUS ID COLUMNS
    |--------------------------------------------------------------------------
    | Menggunakan Subquery addSelect menggantikan leftJoin agar ID tidak bertabrakan
    */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->select('customers.*')
            ->addSelect([
                'nested_group_key' => DB::table('technicians')
                    ->whereColumn('technicians.id', 'customers.technician_id')
                    ->select(DB::raw("CONCAT(COALESCE(technicians.nama_technician, 'TANPA TEKNISI'), ' - ', COALESCE(customers.kota, ''))"))
                    ->limit(1)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}