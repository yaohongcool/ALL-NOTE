<?php

namespace App\Http\Requests\Fund;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFundSkinEarningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'month' => ['required', 'date'],
            'revenue' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }
}
