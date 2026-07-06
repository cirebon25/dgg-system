<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccommodationClaimResource\Pages;
use App\Models\AccommodationClaim;
use App\Models\Technician;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Traits\HasRoleAccess;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class AccommodationClaimResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin', 'keuangan', 'manager', 'teknisi', 'admin_teknik'];

    protected static ?string $model          = AccommodationClaim::class;
    protected static ?string $navigationLabel  = 'Klaim Akomodasi Luar Kota';
    protected static ?string $navigationIcon   = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup  = 'Keuangan';
    protected static ?int    $navigationSort   = 1;
    protected static ?string $modelLabel       = 'Klaim Akomodasi';
    protected static ?string $pluralModelLabel = 'Klaim Akomodasi';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make(' Data Klaim Akomodasi Luar Kota')
                ->schema([

                    Forms\Components\Select::make('technician_id')
                        ->label('Nama Teknisi Pelaksana')
                        ->options(Technician::orderBy('nama_technician')->pluck('nama_technician', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('wilayah')
                        ->label('Wilayah / Kota Tujuan')
                        ->placeholder('Contoh: INDRAMAYU')
                        ->required(),

                    Forms\Components\DatePicker::make('dari_tanggal')
                        ->label('Tanggal Berangkat')
                        ->required()
                        ->live(),

                    Forms\Components\DatePicker::make('sampai_tanggal')
                        ->label('Tanggal Kembali')
                        ->required()
                        ->live(),

                ])->columns(2),

            Forms\Components\Section::make(' Rincian Biaya Pengeluaran')
                ->schema([

                    Forms\Components\TextInput::make('biaya_transportasi')
                        ->label('Biaya Transportasi (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(30000)
                        ->live(onBlur: true),

                    Forms\Components\TextInput::make('konsumsi_karyawan')
                        ->label('Biaya Konsumsi (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(20000)
                        ->live(onBlur: true),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('keterangan_lain_1')
                            ->label('Keterangan Biaya Tambahan')
                            ->placeholder('Contoh: Parkir, Tol...'),
                        Forms\Components\TextInput::make('pengeluaran_lain_1')
                            ->label('Nominal Biaya Tambahan (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true),
                    ]),

                    Forms\Components\Placeholder::make('total_preview')
                        ->label('Total Keseluruhan Biaya')
                        ->content(function (Forms\Get $get): string {
                            $total =
                                (float)($get('biaya_transportasi') ?? 0) +
                                (float)($get('konsumsi_karyawan') ?? 0) +
                                (float)($get('pengeluaran_lain_1') ?? 0) +
                                (float)($get('pengeluaran_lain_2') ?? 0);
                            return '= Rp. ' . number_format($total, 0, ',', '.');
                        })
                        ->columnSpanFull(),

                ])->columns(2),

            Forms\Components\Section::make('Daftar Kunjungan Customer')
                ->description('Isi daftar customer yang dikunjungi selama perjalanan luar kota ini.')
                ->schema([

                    Forms\Components\Repeater::make('visits')
                        ->relationship('visits')
                        ->schema([
                            Forms\Components\TextInput::make('no_urut')
                                ->label('No')
                                ->numeric()
                                ->default(1)
                                ->columnSpan(1),

                            Forms\Components\Toggle::make('manual')
                                ->label('')
                                ->default(false)
                                ->live()
                                ->columnSpan(1),

                            // Mode dropdown
                            Forms\Components\Select::make('nama_customer')
                                ->label('Nama Customer')
                                ->options(fn() => \App\Models\Customer::orderBy('nama_customer')->pluck('nama_customer', 'nama_customer'))
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if ($state) {
                                        $customer = \App\Models\Customer::where('nama_customer', $state)->first();
                                        $set('alamat', $customer?->alamat ?? '');
                                    }
                                })
                                ->visible(fn(Forms\Get $get) => ! $get('manual'))
                                ->columnSpan(4),

                            Forms\Components\TextInput::make('alamat')
                                ->label('Alamat / Kota')
                                ->required()
                                ->visible(fn(Forms\Get $get) => ! $get('manual'))
                                ->columnSpan(3),

                            // Mode manual
                            Forms\Components\TextInput::make('nama_customer')
                                ->label('Nama Tempat / Kanvas')
                                ->placeholder('Ketik nama tempat...')
                                ->required()
                                ->visible(fn(Forms\Get $get) => $get('manual'))
                                ->columnSpan(4),

                            Forms\Components\TextInput::make('alamat')
                                ->label('Alamat / Kota')
                                ->placeholder('Ketik alamat...')
                                ->required()
                                ->visible(fn(Forms\Get $get) => $get('manual'))
                                ->columnSpan(3),

                            Forms\Components\Select::make('keterangan')
                                ->label('Keterangan')
                                ->options([
                                    'RM'        => 'RM',
                                    'RN'        => 'RN',
                                    'CM'        => 'CM',
                                    'RR'        => 'RR',
                                    'PENAWARAN' => 'PENAWARAN',
                                    'PENARIKAN' => 'PENARIKAN',
                                    'KONTRAK'   => 'BUKU KONTRAK',
                                    'KANVAS'    => 'KANVAS',
                                    'LAINNYA'   => 'LAINNYA',
                                ])
                                ->required()
                                ->columnSpan(2),
                        ])
                        ->columns(11)
                        ->addActionLabel('+ Tambah Kunjungan')
                        ->defaultItems(1)
                        ->reorderable()
                        ->columnSpanFull(),

                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Dibuat')->date('d/m/Y')->sortable(),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')->searchable()->weight('bold'),

                Tables\Columns\TextColumn::make('wilayah')
                    ->label('Wilayah')->searchable(),

                Tables\Columns\TextColumn::make('dari_tanggal')
                    ->label('Tgl Berangkat')->date('d/m/Y'),

                Tables\Columns\TextColumn::make('sampai_tanggal')
                    ->label('Tgl Kembali')->date('d/m/Y'),

                Tables\Columns\TextColumn::make('lama_hari')
                    ->label('Lama')->suffix(' Hari'),

                Tables\Columns\TextColumn::make('total_biaya')
                    ->label('Total Biaya')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Draft'     => 'gray',
                        'Diajukan'  => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak'   => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft'     => 'Draft',
                        'Diajukan'  => 'Diajukan',
                        'Disetujui' => 'Disetujui',
                        'Ditolak'   => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('technician_id')
                    ->label('Teknisi')
                    ->relationship('technician', 'nama_technician'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(AccommodationClaim $record): string => route('cetak.klaim-akomodasi', $record->id))
                    ->openUrlInNewTab(),

                Action::make('ajukan')
                    ->label('Ajukan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn(AccommodationClaim $record) => $record->status === 'Draft')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Klaim?')
                    ->modalDescription('Klaim akan dikirim ke pimpinan untuk persetujuan.')
                    ->action(function (AccommodationClaim $record) {
                        $record->update(['status' => 'Diajukan']);
                        Notification::make()->title('Klaim berhasil diajukan!')->success()->send();
                    }),

                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(AccommodationClaim $record) => $record->status === 'Diajukan')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Klaim Ini?')
                    ->action(function (AccommodationClaim $record) {
                        $record->update(['status' => 'Disetujui']);
                        Notification::make()->title('Klaim disetujui!')->success()->send();
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(AccommodationClaim $record) => $record->status === 'Diajukan')
                    ->form([
                        Forms\Components\Textarea::make('catatan_penolakan')
                            ->label('Alasan Penolakan')->required(),
                    ])
                    ->action(function (AccommodationClaim $record, array $data) {
                        $record->update([
                            'status'             => 'Ditolak',
                            'catatan_penolakan'  => $data['catatan_penolakan'],
                        ]);
                        Notification::make()->title('Klaim ditolak.')->danger()->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccommodationClaims::route('/'),
            'create' => Pages\CreateAccommodationClaim::route('/create'),
            'edit'   => Pages\EditAccommodationClaim::route('/{record}/edit'),
        ];
    }
}