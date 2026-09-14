<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartBorrowingHeaderResource\Pages;
use App\Models\PartBorrowingHeader;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;

class PartBorrowingHeaderResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = PartBorrowingHeader::class;

    protected static ?string $navigationLabel = 'Pinjam Part';
    protected static ?string $pluralModelLabel = 'Pinjam Part';
    protected static ?string $modelLabel = 'Pinjam Part';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Formulir Peminjaman Barang')
                    ->description('Pilih teknisi lalu tambahkan sparepart yang dipinjam. Maksimal 10 item.')
                    ->schema([

                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Nama Teknisi')
                            ->required()
                            ->searchable()
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('items')
                            ->label('Daftar Sparepart yang Dipinjam')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('sparepart_id')
                                    ->label('Sparepart')
                                    ->options(
                                        Sparepart::where('stok', '>', 0)
                                            ->orderBy('nama_sparepart')
                                            ->get()
                                            ->mapWithKeys(fn($s) => [
                                                $s->id => $s->nama_sparepart . ($s->nama_alias ? " — {$s->nama_alias}" : '')
                                            ])
                                            ->toArray()
                                    )
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn(Forms\Set $set) => $set('jumlah', 1))
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->rules([
                                        fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $sparepartId = $get('sparepart_id');
                                            if (!$sparepartId) return;
                                            $sparepart = Sparepart::find($sparepartId);
                                            if (!$sparepart) return;
                                            if ($sparepart->stok <= 0) {
                                                $fail("Stok gudang habis (0).");
                                            }
                                            if ((int)$value > $sparepart->stok) {
                                                $fail("Melebihi stok gudang. Sisa: {$sparepart->stok} pcs.");
                                            }
                                        },
                                    ])
                                    ->columnSpan(1),

                                Forms\Components\Placeholder::make('info_stok')
                                    ->label('Stok Gudang')
                                    ->content(function (Forms\Get $get): string {
                                        $id = $get('sparepart_id');
                                        if (!$id) return '-';
                                        $s = Sparepart::find($id);
                                        return $s ? $s->stok . ' pcs' : '-';
                                    })
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->minItems(1)
                            ->maxItems(10)
                            ->addActionLabel('+ Tambah Sparepart')
                            ->reorderable(false)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan (opsional)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
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
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('cetak.bukti-pinjam-multi', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPartBorrowingHeaders::route('/'),
            'create' => Pages\CreatePartBorrowingHeader::route('/create'),
        ];
    }
}