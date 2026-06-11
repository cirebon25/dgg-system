<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashReceiptResource\Pages;
use App\Models\CashReceipt;
use App\Models\CashLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Filament\Traits\HasRoleAccess;

class CashReceiptResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model           = CashReceipt::class;
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-down-circle';
    protected static ?string $navigationLabel = 'Input Kas Masuk';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int    $navigationSort  = 9;
    protected static ?string $modelLabel      = 'Kas Masuk';
    protected static ?string $pluralModelLabel = 'Kas Masuk';
    protected static array   $allowedRoles    = ['admin', 'keuangan', 'manager'];

    public static function form(Form $form): Form
    {
        return $form->schema([
        Forms\Components\Section::make('Data Penerimaan Kas')
        ->columns(2)
        ->schema([
        Forms\Components\DatePicker::make('tanggal')
        ->label('Tanggal Penerimaan')
        ->required()
        ->default(now())
        ->native(false),

        Forms\Components\TextInput::make('no_bukti')
        ->label('No. Bukti / Referensi')
        ->placeholder('Otomatis jika kosong')
        ->helperText('Contoh: KM-001/VI/26 • dibuat otomatis jika tidak diisi')
        ->maxLength(30),

        Forms\Components\TextInput::make('sumber_dana')
        ->label('Sumber / Pengirim Dana')
        ->placeholder('Contoh: Bank BCA, Transfer Kantor Pusat...')
        ->required()
        ->maxLength(255),

        Forms\Components\TextInput::make('jumlah')
        ->label('Jumlah Kas Masuk (Rp)')
        ->prefix('Rp')
        ->required()
        ->integer()
        ->minValue(1),

        // REVISI NYATA: Menambahkan ->required() agar user wajib mengisi Uraian/Keterangan
        Forms\Components\Textarea::make('keterangan')
        ->label('Uraian / Keterangan')
        ->placeholder('Contoh: Penerimaan modal operasional bulan Juni...')
        ->required()
        ->rows(3)
        ->columnSpanFull(),

        Forms\Components\TextInput::make('dibuat_oleh')
        ->label('Nama Pembuat')
        ->default(fn() => auth()->user()?->name ?? '')
        ->maxLength(100),
        ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('no_bukti')
                    ->label('No. Bukti')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('sumber_dana')
                    ->label('Sumber Dana')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Uraian')
                    ->searchable()
                    ->wrap()
                    ->limit(60),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Kas Masuk')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('dibuat_oleh')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April',   5 => 'Mei',      6 => 'Juni',
                                7 => 'Juli',    8 => 'Agustus',  9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default(now()->month),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(
                                range(now()->year, now()->year - 3),
                                range(now()->year, now()->year - 3)
                            ))
                            ->default(now()->year),
                    ])
                    ->query(function ($query, array $data) {
                        if (filled($data['bulan']) && filled($data['tahun'])) {
                            $query->whereYear('tanggal', $data['tahun'])
                                  ->whereMonth('tanggal', $data['bulan']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCashReceipts::route('/'),
            'create' => Pages\CreateCashReceipt::route('/create'),
            'edit'   => Pages\EditCashReceipt::route('/{record}/edit'),
        ];
    }
}