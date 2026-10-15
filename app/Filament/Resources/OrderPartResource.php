<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderPartResource\Pages;
use App\Filament\Traits\HasRoleAccess;
use App\Models\OrderPart;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderPartResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = OrderPart::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Gudang & Stok';
    protected static ?string $navigationLabel = 'Order Part';
    protected static ?string $modelLabel = 'Order Part';
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $n = Sparepart::stokMenipis()->count();
        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah sparepart dengan stok menipis';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Order')
                ->schema([
                    Forms\Components\TextInput::make('no_order')
                        ->label('No Order')
                        ->default(fn() => OrderPart::generateNumber())
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\DatePicker::make('tanggal')
                        ->default(now())
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'draft'    => 'Draft',
                            'diajukan' => 'Diajukan',
                            'dipesan'  => 'Dipesan',
                            'diterima' => 'Diterima',
                            'batal'    => 'Batal',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\Textarea::make('catatan')
                        ->columnSpanFull(),
                ])->columns(3),

            Forms\Components\Section::make('Daftar Part yang Diorder')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Forms\Components\Select::make('sparepart_id')
                                ->label('Sparepart')
                                ->options(fn() => SparepartResource::getOptionsWithAlias())
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn($state, Set $set) =>
                                $set('stok_saat_ini', Sparepart::find($state)?->stok ?? 0))
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('stok_saat_ini')
                                ->label('Stok Saat Ini')
                                ->numeric()
                                ->readOnly()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('jumlah_order')
                                ->label('Jumlah Order')
                                ->numeric()
                                ->minValue(1)
                                ->required(),
                            Forms\Components\TextInput::make('keterangan')
                                ->columnSpan(5),
                        ])
                        ->columns(5)
                        ->addActionLabel('Tambah Part')
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('no_order')->label('No Order')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Jumlah Item')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'draft'    => 'gray',
                        'diajukan' => 'warning',
                        'dipesan'  => 'info',
                        'diterima' => 'success',
                        'batal'    => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('catatan')->limit(40)->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'diajukan' => 'Diajukan',
                    'dipesan' => 'Dipesan',
                    'diterima' => 'Diterima',
                    'batal' => 'Batal',
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generateLowStock')
                    ->label('Buat Draft dari Stok Menipis')
                    ->icon('heroicon-o-bolt')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Sistem akan membuat draft order untuk semua sparepart yang stoknya sudah di bawah/sama dengan stok minimum. Draft masih bisa diedit.')
                    ->action(function () {
                        $order = OrderPart::createDraftFromLowStock();

                        if (! $order) {
                            Notification::make()
                                ->title('Tidak ada sparepart yang perlu diorder')
                                ->body('Semua stok aman, atau sudah ada di order yang masih aktif.')
                                ->info()->send();
                            return;
                        }

                        Notification::make()
                            ->title("Draft {$order->no_order} dibuat")
                            ->success()->send();

                        return redirect(static::getUrl('edit', ['record' => $order]));
                    }),

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(OrderPart $record) => route('print.order-part', $record))
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
            'index'  => Pages\ListOrderParts::route('/'),
            'create' => Pages\CreateOrderPart::route('/create'),
            'edit'   => Pages\EditOrderPart::route('/{record}/edit'),
        ];
    }
}