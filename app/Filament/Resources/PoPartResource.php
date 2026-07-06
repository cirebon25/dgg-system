<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoPartResource\Pages;
use App\Models\PoPart;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PoPartResource extends Resource
{
    protected static ?string $model = PoPart::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'PO Part';
    protected static ?string $modelLabel = 'PO Part';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Header PO')
                    ->schema([
                        Forms\Components\TextInput::make('no_po')
                            ->label('No PO')
                            ->default(fn() => PoPart::generateNoPo())
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Item Part')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Toggle::make('dari_dropdown')
                                    ->label('Pilih dari data sparepart')
                                    ->default(true)
                                    ->live()
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('sparepart_id')
                                    ->label('Pilih Sparepart')
                                    ->options(Sparepart::orderBy('nama_sparepart')->pluck('nama_sparepart', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if ($state) {
                                            $sp = Sparepart::find($state);
                                            if ($sp) {
                                                $set('nama_part', $sp->nama_sparepart);
                                                $set('kode_part', $sp->code_part ?? '');
                                                $set('merk_type', '');
                                            }
                                        }
                                    })
                                    ->visible(fn(Get $get) => $get('dari_dropdown'))
                                    ->columnSpan(2),

                                // Satu field nama_part saja -- diisi otomatis dari dropdown
                                // atau diketik manual. Selalu visible, selalu tersimpan ke DB.
                                Forms\Components\TextInput::make('nama_part')
                                    ->label('Nama Part')
                                    ->required()
                                    ->live()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('merk_type')
                                    ->label('Merk / Type')
                                    ->nullable(),

                                Forms\Components\TextInput::make('kode_part')
                                    ->label('Kode Part')
                                    ->nullable(),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required(),

                                Forms\Components\TextInput::make('keterangan')
                                    ->label('Keterangan')
                                    ->nullable()
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->addActionLabel('+ Tambah Item')
                            ->reorderable(false)
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_po')
                    ->label('No PO')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn(PoPart $record) => route('po-part.print', $record->id))
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPoParts::route('/'),
            'create' => Pages\CreatePoPart::route('/create'),
            'edit'   => Pages\EditPoPart::route('/{record}/edit'),
        ];
    }
}
