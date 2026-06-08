<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashMutationResource\Pages;
use App\Models\CashMutation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;

class CashMutationResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'keuangan', 'manager', 'teknisi'];

    protected static ?string $model = CashMutation::class;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Form SPM';
    protected static ?string $navigationGroup  = 'Keuangan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Utama')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Pengeluaran')
                        ->default(now())
                        ->required(),
                    Forms\Components\Hidden::make('jenis_pembayaran')->default('Tunai'),
                ]),

            Forms\Components\Section::make('Daftar Uraian Pengeluaran')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            Forms\Components\TextInput::make('uraian')
                                ->label('Uraian Pembayaran')
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('plat_nomor')
                                ->label('Plat Nomor')
                                ->placeholder('E 1234 AB'),

                            Forms\Components\TextInput::make('km_awal')
                                ->label('KM Awal')
                                ->numeric(),

                            Forms\Components\TextInput::make('km_akhir')
                                ->label('KM Akhir')
                                ->numeric(),

                            Forms\Components\TextInput::make('kode_perkiraan')
                                ->label('Kode Perkiraan'),

                            Forms\Components\TextInput::make('jumlah')
                                ->label('Jumlah (Rp)')
                                ->numeric()
                                ->required()
                                ->live(debounce: 500)
                                ->afterStateUpdated(function (callable $set, callable $get) {
                                    self::recalculateTotal($set, $get);
                                }),
                        ])
                        ->columns(4)
                        ->addActionLabel('Tambah Baris Uraian')
                        ->defaultItems(1)
                        ->live()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            self::recalculateTotal($set, $get);
                        }),
                ]),

            Forms\Components\Section::make('Total & Otorisasi')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('total_jumlah')
                        ->label('Total Jumlah Rp.')
                        ->numeric()
                        ->readOnly()
                        ->prefix('Rp.'),

                    Forms\Components\TextInput::make('terbilang')
                        ->label('Terbilang Otomatis')
                        ->readOnly()
                        ->placeholder('Akan terisi otomatis...'),

                    Forms\Components\TextInput::make('pembuat')
                        ->label('Dibuat Oleh')
                        ->default('RUDI'),

                    Forms\Components\TextInput::make('pemeriksa')
                        ->label('Diketahui Oleh')
                        ->default('RIZEN'),

                    Forms\Components\TextInput::make('penerima')
                        ->label('Penerima')
                        ->default('RUDI'),
                ]),
        ]);
    }

    protected static function recalculateTotal(callable $set, callable $get): void
    {
        $items = $get('items') ?? [];
        $total = collect($items)->sum(fn($item) => (float) ($item['jumlah'] ?? 0));

        $set('total_jumlah', $total);

        // Cek jika class CashMutation ada sebelum panggil konversiTerbilang
        if (class_exists(CashMutation::class)) {
            $set('terbilang', CashMutation::konversiTerbilang($total) . ' rupiah');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_voucher')
                    ->label('No. Voucher')
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('total_jumlah')
                    ->label('Total')
                    ->money('IDR')
                    ->default(0),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('cash-mutation.print', $record->id))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCashMutations::route('/'),
            'create' => Pages\CreateCashMutation::route('/create'),
            'edit'   => Pages\EditCashMutation::route('/{record}/edit'),
        ];
    }
}
