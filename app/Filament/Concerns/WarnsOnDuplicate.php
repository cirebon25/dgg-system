<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

trait WarnsOnDuplicate
{
    public ?string $pesanDuplikat = null;

    // Diisi di tiap halaman Create
    abstract protected function findDuplicateRecord(array $data): ?Model;

    // Dialog di tengah layar
    public function duplikatAction(): Action
    {
        return Action::make('duplikat')
            ->modalHeading('Data serupa sudah ada, tidak bisa disimpan')
            ->modalDescription(fn() => $this->pesanDuplikat)
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor('danger')
            ->modalWidth('md')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Mengerti');
    }

    protected function beforeCreate(): void
    {
        $dup = $this->findDuplicateRecord($this->data);

        if (! $dup) {
            return;
        }

        $label = class_basename($dup)
            . ($dup->tipe_kunjungan ? " ({$dup->tipe_kunjungan})" : '');

        $this->pesanDuplikat = "Sudah ada {$label} dengan mesin, customer, dan tanggal yang sama, "
            . 'diinput ' . $dup->created_at->locale('id')->diffForHumans()
            . '. Cek data yang sudah ada terlebih dahulu.';

        $this->mountAction('duplikat');

        // Batalkan penyimpanan
        $this->halt();
    }
}
