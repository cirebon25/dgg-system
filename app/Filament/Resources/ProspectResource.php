<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectResource\Pages;
use App\Models\Prospect;
use App\Models\ProspectVisit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Filament\Traits\HasRoleAccess;


class ProspectResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];
    protected static ?string $model = Prospect::class;
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Kunjungan Sales';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Perusahaan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_perusahaan')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $record) {
                                if (!$state) return;

                                // Cek apakah perusahaan sudah pernah dikunjungi
                                $existing = Prospect::where('nama_perusahaan', 'like', "%{$state}%")
                                    ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                    ->with(['visits.marketing'])
                                    ->first();

                                if ($existing && $existing->visits->isNotEmpty()) {
                                    $histori = $existing->visits->map(function ($v) {
                                        return "{$v->marketing?->nama_marketing} ({$v->tanggal_kunjungan->format('d/m/Y')}) — {$v->hasil_kunjungan}";
                                    })->implode(' | ');

                                    Notification::make()
                                        ->title('⚠️ Perusahaan ini sudah pernah dikunjungi!')
                                        ->body("Histori kunjungan: {$histori}")
                                        ->warning()
                                        ->persistent()
                                        ->send();
                                }
                            }),

                        Forms\Components\TextInput::make('kota')
                            ->label('Kota')
                            ->nullable(),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->nullable()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('pic_nama')
                            ->label('Nama PIC')
                            ->nullable(),

                        Forms\Components\TextInput::make('pic_jabatan')
                            ->label('Jabatan PIC')
                            ->nullable(),

                        Forms\Components\TextInput::make('pic_telp')
                            ->label('No Telp PIC')
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->label('Status Prospek')
                            ->options([
                                'Baru'    => 'Baru',
                                'Proses'  => 'Proses',
                                'Closing' => 'Closing',
                                'Gagal'   => 'Gagal',
                            ])
                            ->default('Baru')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Riwayat Kunjungan')
                    ->schema([
                        Forms\Components\Repeater::make('visits')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('marketing_id')
                                    ->relationship('marketing', 'nama_marketing')
                                    ->label('Marketing')
                                    ->required()
                                    ->searchable(),

                                Forms\Components\DatePicker::make('tanggal_kunjungan')
                                    ->label('Tanggal Kunjungan')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\TextInput::make('jenis_mesin_existing')
                                    ->label('Jenis Mesin Existing')
                                    ->placeholder('Contoh: Fotocopy A3'),

                                Forms\Components\TextInput::make('merk_mesin_existing')
                                    ->label('Merk Mesin Existing')
                                    ->placeholder('Contoh: Kyocera, Canon'),

                                Forms\Components\Select::make('hasil_kunjungan')
                                    ->label('Hasil Kunjungan')
                                    ->options([
                                        'Interest'   => 'Kunjungan awal',
                                        'Follow Up'  => 'Follow Up',
                                        'Closing'    => 'Closing',
                                        'Gagal'      => 'Gagal',
                                    ])
                                    ->default('Follow Up')
                                    ->required(),

                                Forms\Components\Textarea::make('catatan')
                                    ->label('Catatan')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('+ Tambah Kunjungan')
                            ->defaultItems(1)
                            ->orderColumn('tanggal_kunjungan')
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_perusahaan')
                    ->label('Perusahaan')
                    ->searchable()
                    ->description(fn($record) => $record->kota ?? '-'),

                Tables\Columns\TextColumn::make('pic_nama')
                    ->label('PIC')
                    ->description(fn($record) => $record->pic_jabatan ?? '-'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Baru'    => 'gray',
                        'Proses'  => 'warning',
                        'Closing' => 'success',
                        'Gagal'   => 'danger',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('visits_count')
                    ->label('Jumlah Visit')
                    ->counts('visits')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('lastVisit.marketing.nama_marketing')
                    ->label('Visit Terakhir Oleh'),

                Tables\Columns\TextColumn::make('lastVisit.tanggal_kunjungan')
                    ->label('Tgl Visit Terakhir')
                    ->date('d/m/Y'),
            ])

            ->headerActions([
                Tables\Actions\Action::make('cetakLaporan')
                    ->label('Cetak Laporan Sales')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->required()
                            ->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('prospect.laporan', $data)),

                Tables\Actions\CreateAction::make(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Baru'    => 'Baru',
                        'Proses'  => 'Proses',
                        'Closing' => 'Closing',
                        'Gagal'   => 'Gagal',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withCount('visits')
            ->with(['lastVisit.marketing']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProspects::route('/'),
            'create' => Pages\CreateProspect::route('/create'),
            'edit'   => Pages\EditProspect::route('/{record}/edit'),
        ];
    }
}