<?php

namespace App\Traits;

trait UppercaseAttributes
{
    /**
     * Mengubah string values menjadi uppercase
     * Exclude fields yang tidak ingin di-uppercase dengan define:
     * protected $uppercaseExcept = ['counter_bw', 'counter_color', ...];
     */
    public function setAttribute($key, $value)
    {
        // ✅ Cek apakah property $uppercaseExcept ada di model
        // Kalau tidak ada, gunakan empty array
        $except = property_exists($this, 'uppercaseExcept')
            ? $this->uppercaseExcept
            : [];

        // ✅ Kalau field ada di exclude list, skip uppercase
        if (in_array($key, $except)) {
            return parent::setAttribute($key, $value);
        }

        // ✅ Kalau string & bukan di exclude, uppercase
        if (is_string($value)) {
            $value = strtoupper($value);
        }

        return parent::setAttribute($key, $value);
    }
}
