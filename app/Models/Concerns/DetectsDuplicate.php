<?php

namespace App\Models\Concerns;

trait DetectsDuplicate
{
    // Kolom yang dicocokkan persis
    abstract public static function duplicateKeys(): array;

    // Kolom tanggal yang dicocokkan per hari
    abstract public static function duplicateDateKeys(): array;

    public static function findDuplicate(array $data, $ignoreId = null)
    {
        $query = static::query();

        foreach (static::duplicateKeys() as $column) {
            $query->where($column, $data[$column] ?? null);
        }

        foreach (static::duplicateDateKeys() as $column) {
            $query->whereDate($column, $data[$column] ?? null);
        }

        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->latest()->first();
    }
}
