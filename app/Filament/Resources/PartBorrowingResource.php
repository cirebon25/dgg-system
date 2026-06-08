<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartBorrowingResource\Pages;
use App\Models\PartBorrowing;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;

class PartBorrowingResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'manager'];

    protected static ?string $model = PartBorrowing::class;

    protected static ?string $navigationLabel = 'Pinjam Part';
    protected static ?string $pluralModelLabel = 'Pinjam Part';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Formulir Peminjaman Barang')
                    ->description('Barang akan memotong stok gudang dan MENAMBAH stok di Tas Teknisi.')
                    ->schema([
                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Nama Teknisi')
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('sparepart_id')
                            ->relationship(
                                name: 'sparepart',
                                titleAttribute: 'nama_sparepart',
                                modifyQueryUsing: fn($query) => $query->where('stok', '>', 0)
                            )
                            ->label('Pilih Sparepart')
                            ->required()
                            ->searchable()
                            ->live(),

                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah Pinjam')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->rules([
                                fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $sparepartId = $get('sparepart_id');
                                    if (!$sparepartId) return;

                                    $sparepart = Sparepart::find($sparepartId);
                                    if (!$sparepart) return;

                                    if ($sparepart->stok <= 0) {
                                        $fail("Saldo gudang untuk sparepart ini sudah habis (0).");
                                    }

                                    if ($value > $sparepart->stok) {
                                        $fail("Jumlah pinjam ({$value}) melebihi saldo gudang (Sisa: {$sparepart->stok}).");
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
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Sparepart')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->badge()
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(PartBorrowing $record) => route('cetak.bukti-pinjam', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartBorrowings::route('/'),
            'create' => Pages\CreatePartBorrowing::route('/create'),
        ];
    }
}
