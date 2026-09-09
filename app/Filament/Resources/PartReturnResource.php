<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartReturnResource\Pages;
use App\Models\PartReturn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;

class PartReturnResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = PartReturn::class;

    protected static ?string $navigationLabel = 'Retur Part';
    protected static ?string $pluralModelLabel = 'Retur Part';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-on-rectangle';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Formulir Pengembalian Barang')
                    ->description('Barang akan menambah stok gudang dan MENGURANGI stok di Tas Teknisi.')
                    ->schema([
                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Nama Teknisi')
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('sparepart_id')
                            ->relationship('sparepart', 'nama_sparepart')
                            ->label('Pilih Sparepart')
                            ->required()
                            ->searchable(),
                        // Cari bagian TextInput::make('jumlah') lalu ubah jadi seperti ini:

                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah Retur')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->reactive()
                            ->rules([
                                fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $techId = $get('technician_id');
                                    $partId = $get('sparepart_id');

                                    if (!$techId || !$partId) return;

                                    $stock = \App\Models\TechnicianStock::where('technician_id', $techId)
                                        ->where('sparepart_id', $partId)
                                        ->first();

                                    // MENGGUNAKAN 'jumlah' sesuai hasil data Tinker Anda
                                    $currentStock = $stock ? $stock->jumlah : 0;

                                    if ($value > $currentStock) {
                                        $fail("Gagal! Stok di tas Teknisi tidak mencukupi. Sisa saat ini: {$currentStock}");
                                    }
                                },
                            ]),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')->searchable(),
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Sparepart')->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')->badge()->color('danger'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartReturns::route('/'),
            'create' => Pages\CreatePartReturn::route('/create'),
        ];
    }
}
