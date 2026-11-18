<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year'  => ['nullable', 'integer', 'between:2020,' . (now()->year + 1)],
        ];
    }
}