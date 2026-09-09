<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintFormResource\Pages;
use App\Models\PrintForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasRoleAccess;

class PrintFormResource extends Resource
{
    use HasRoleAccess;

    // Masukkan semua role yang diizinkan di sini (sesuaikan penulisan hurufnya dengan data di database Anda)
    protected static array $allowedRoles = ['admin', 'teknisi', 'admin_teknik'];

    protected static ?string $model = PrintForm::class;
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Upload Form Cetak baru';
    protected static ?string $navigationGroup = 'Pusat Cetak';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul Form')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->nullable()
                    ->rows(3),

                Forms\Components\FileUpload::make('file_path')
                    ->label('File PDF')
                    ->required()
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('print-forms')
                    ->preserveFilenames()
                    ->maxSize(10240),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Form')
                    ->searchable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Upload')
                    ->date('d M Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrintForms::route('/'),
            'create' => Pages\CreatePrintForm::route('/create'),
            'edit' => Pages\EditPrintForm::route('/{record}/edit'),
        ];
    }
}
