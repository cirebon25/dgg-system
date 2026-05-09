<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date('d/M/Y')->sortable(),
                Tables\Columns\TextColumn::make('machine.serial_number')->label('SN')->searchable(),

                // Kolom Counter yang Baru Diinput
                Tables\Columns\TextColumn::make('counter_color')->label('C-Color')->numeric(),
                Tables\Columns\TextColumn::make('counter_bw')->label('C-BW')->numeric(),

                // KOLOM PEMAKAIAN (Yang Anda maksud)
                Tables\Columns\TextColumn::make('usage_color')
                    ->label('Pakai Color')
                    ->badge()
                    ->color('success')
                    ->description(fn ($record) => 'Lembar'),

                Tables\Columns\TextColumn::make('usage_bw')
                    ->label('Pakai BW')
                    ->badge()
                    ->color('info')
                    ->description(fn ($record) => 'Lembar'),

                Tables\Columns\TextColumn::make('technician.nama_technician')->label('Teknisi'),
            ])
            ->filters([
                // Tambahkan ini biar bisa filter bulanan buat arsip
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari'),
                        Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(fn ($query, array $data) => $query->when($data['dari'], fn ($q) => $q->whereDate('tanggal', '>=', $data['dari']))
                        ->when($data['sampai'], fn ($q) => $q->whereDate('tanggal', '<=', $data['sampai']))),
            ]);
    }
};
