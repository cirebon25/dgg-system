<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen User';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int    $navigationSort  = 99;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi User')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('role')
                        ->label('Role / Hak Akses')
                        ->options([
                            'admin'    => 'Admin',
                            'manager'  => 'Manager',
                            'teknisi'  => 'Teknisi',
                            'keuangan' => 'Keuangan',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn(string $context) => $context === 'create')
                        ->helperText('Kosongkan jika tidak ingin mengubah password (saat edit)')
                        ->maxLength(255),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'danger'  => 'admin',
                        'warning' => 'manager',
                        'success' => 'teknisi',
                        'info'    => 'keuangan',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin'    => 'Admin',
                        'manager'  => 'Manager',
                        'teknisi'  => 'Teknisi',
                        'keuangan' => 'Keuangan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (User $record) {
                        // Cegah hapus diri sendiri
                        if ($record->id === auth()->id()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Tidak bisa menghapus akun sendiri!')
                                ->danger()
                                ->send();
                            throw new \Exception('Cannot delete own account');
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}