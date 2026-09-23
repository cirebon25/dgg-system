<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Status Invoice';
    protected static ?string $navigationGroup = 'MRC & Billing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Input Tanggal diletakkan di atas agar bisa mendeteksi perubahan
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Invoice')
                    ->required()
                    ->default(now())
                    ->reactive() // Membuat form peka terhadap perubahan tanggal
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!$state) return;

                        // Ambil tahun (2 digit terakhir) dan bulan dari tanggal yang dipilih
                        $yearMonth = date('ym', strtotime($state));

                        // Cari nomor urut terakhir pada bulan dan tahun tersebut
                        $lastInvoice = Invoice::where('invoice_number', 'like', "FB{$yearMonth}%")
                            ->latest('id')
                            ->first();

                        $sequence = 1;
                        if ($lastInvoice) {
                            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
                            $sequence = $lastNumber + 1;
                        }

                        // Set ulang nilai no. invoice secara otomatis
                        $set('invoice_number', 'FB' . $yearMonth . str_pad($sequence, 4, '0', STR_PAD_LEFT));
                    }),

                // No. Invoice otomatis menyesuaikan tanggal, tapi tetap bisa diedit manual jika perlu
                Forms\Components\TextInput::make('invoice_number')
                    ->label('No. Invoice')
                    ->default(function () {
                        $yearMonth = date('ym');
                        $lastInvoice = Invoice::where('invoice_number', 'like', "FB{$yearMonth}%")->latest('id')->first();

                        $sequence = 1;
                        if ($lastInvoice) {
                            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
                            $sequence = $lastNumber + 1;
                        }

                        return 'FB' . $yearMonth . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                    })
                    ->required()
                    ->maxLength(255),

                // Menampilkan list semua nama customer
                Forms\Components\Select::make('customer_id')
                    ->label('Nama Customer')
                    ->relationship('customer', 'nama_customer')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('nominal')
                    ->label('Nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\TextInput::make('nama_pengirim')
                    ->label('Nama Pengirim')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama_penerima')
                    ->label('Nama Penerima')
                    ->maxLength(255),

                // Centang status invoice di dalam form
                Forms\Components\Toggle::make('is_received')
                    ->label('Invoice Sudah Diterima Konsumen')
                    ->default(true) // Set default aktif (centang) jika ingin otomatis tercentang saat buat baru
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('received_at', $state ? now() : null);
                    }),

                Forms\Components\DateTimePicker::make('received_at')
                    ->label('Tanggal & Waktu Diterima')
                    ->default(now()) // Otomatis isi hari ini dan jam sekarang
                    ->visible(fn($get) => $get('is_received'))
                    ->required(fn($get) => $get('is_received')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Nama Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_pengirim')
                    ->label('Pengirim')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('nama_penerima')
                    ->label('Penerima')
                    ->searchable()
                    ->placeholder('-'),

                // Kolom Icon/Badge status centang (bukan toggle interaktif di tabel)
                Tables\Columns\IconColumn::make('is_received')
                    ->label('Sudah Diterima')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('received_at')
                    ->label('Tgl Diterima')
                    ->date('d/m/Y H:i')
                    ->placeholder('Belum diterima'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_received')
                    ->label('Status Penerimaan')
                    ->placeholder('Semua Invoice')
                    ->trueLabel('Sudah Diterima Konsumen')
                    ->falseLabel('Belum Diterima'),
            ])
            ->headerActions([
                // Tombol untuk mencetak semua data rekap sekaligus di bagian atas tabel
                Tables\Actions\Action::make('printAll')
                    ->label('Cetak Rekap Semua')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(route('invoice.print-all'))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Tombol Print untuk mencetak per satuan baris
                Tables\Actions\Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Invoice $record): string => route('invoice.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
