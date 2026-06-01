<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeploymentResource\Pages;
use App\Models\Deployment;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $pluralLabel = 'Pemasangan Mesin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pemasangan')
                    ->description('Detail customer dan mesin yang akan dipasang.')
                    ->schema([
                        Forms\Components\TextInput::make('no_kontrak')
                            ->label('No. Kontrak')
                            ->placeholder('Contoh: KTR-2026-001')
                            ->nullable(),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'nama_customer')
                            ->label('Customer')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('machine_id')
                            ->relationship('machine', 'serial_number', function (Builder $query) {
                                return $query->where('status', 'Ready');
                            })
                            ->label('SN Mesin')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->serial_number} - {$record->tipe_model}")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('technician_id')
                            ->relationship('technician', 'nama_technician')
                            ->label('Teknisi Pasang')
                            ->required()
                            ->preload(),

                        Forms\Components\TextInput::make('counter_bw')
                            ->label('Counter Awal BW')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('counter_color')
                            ->label('Counter Awal Color (CL)')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('volt')
                            ->label('Tegangan Listrik (Volt)')
                            ->numeric()
                            ->default(220)
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_instal')
                            ->label('Tanggal Pasang')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Sparepart Tambahan')
                    ->description('Item yang disertakan dalam pengiriman.')
                    ->schema([
                        Forms\Components\Repeater::make('spareparts_custom')
                            ->label('Daftar Suku Cadang')
                            ->schema([
                                Forms\Components\Select::make('sparepart_id')
                                    ->label('Nama Barang')
                                    ->options(Sparepart::pluck('nama_sparepart', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn($set) => $set('jumlah', 1)),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->rules([
                                        fn(Forms\Get $get, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            $sparepartId = $get('sparepart_id');
                                            if (!$sparepartId) return;

                                            $sp = Sparepart::find($sparepartId);
                                            if (!$sp) return;

                                            $kuotaLama = 0;
                                            if ($record) {
                                                $kuotaLama = (int) DB::table('deployment_sparepart')
                                                    ->where('deployment_id', $record->id)
                                                    ->where('sparepart_id', $sparepartId)
                                                    ->value('jumlah');
                                            }

                                            $maksimalTersedia = $sp->stok + $kuotaLama;

                                            if ($maksimalTersedia <= 0) {
                                                $fail("Gagal Simpan! Saldo Gudang untuk item ini sudah HABIS (0).");
                                            } elseif ($value > $maksimalTersedia) {
                                                $fail("Gagal Simpan! Saldo tidak mencukupi. Hanya ada sisa: {$maksimalTersedia} Pcs.");
                                            }
                                        },
                                    ]),
                            ])
                            ->columns(2)
                            ->createItemButtonLabel('Tambah Sparepart')

                            ->formatStateUsing(function ($record) {
                                if (!$record) return [];
                                return DB::table('deployment_sparepart')
                                    ->where('deployment_id', $record->id)
                                    ->get(['sparepart_id', 'jumlah'])
                                    ->map(fn($item) => (array) $item)
                                    ->toArray();
                            })

                            ->saveRelationshipsUsing(function ($record, $state) {
                                $oldItems = DB::table('deployment_sparepart')
                                    ->where('deployment_id', $record->id)
                                    ->get(['sparepart_id', 'jumlah']);

                                foreach ($oldItems as $old) {
                                    Sparepart::where('id', $old->sparepart_id)
                                        ->increment('stok', $old->jumlah);
                                }

                                DB::table('deployment_sparepart')
                                    ->where('deployment_id', $record->id)
                                    ->delete();

                                if (is_array($state)) {
                                    foreach ($state as $item) {
                                        DB::table('deployment_sparepart')->insert([
                                            'deployment_id' => $record->id,
                                            'sparepart_id'  => $item['sparepart_id'],
                                            'jumlah'        => $item['jumlah'],
                                            'created_at'    => now(),
                                            'updated_at'    => now(),
                                        ]);

                                        Sparepart::where('id', $item['sparepart_id'])
                                            ->decrement('stok', $item['jumlah']);
                                    }
                                }
                            }),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan Tambahan')
                            ->placeholder('Contoh: Lantai 2, dekat meja admin')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('machine.serial_number')
                    ->label('SN Mesin')
                    ->description(fn(Deployment $record): string => $record->machine->tipe_model ?? '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('technician.nama_technician')
                    ->label('Teknisi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_instal')
                    ->label('Tgl Pasang')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('machine.status')
                    ->label('Status Unit')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Ready'     => 'success',
                        'Rented'    => 'warning',
                        'Refurbish' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer')
                    ->relationship('customer', 'nama_customer'),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak_sj')
                    ->label('Cetak Surat Jalan')
                    ->icon('heroicon-m-printer')
                    ->color('success')
                    ->url(fn(Deployment $record): string => route('cetak.surat-jalan', ['id' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ← OPTIMASI: Eager load relasi yang ditampilkan di tabel
    // Mencegah N+1 query (dari ratusan query → jadi 3 query saja)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'machine', 'technician']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDeployments::route('/'),
            'create' => Pages\CreateDeployment::route('/create'),
            'edit'   => Pages\EditDeployment::route('/{record}/edit'),
        ];
    }
}
