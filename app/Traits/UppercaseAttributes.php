<?php

namespace App\Traits;

trait UppercaseAttributes
{
    public function setAttribute($key, $value)
    {
        if (is_string($value)) {
            $value = strtoupper($value);
        }
        return parent::setAttribute($key, $value);
    }
}
