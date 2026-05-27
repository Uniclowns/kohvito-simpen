<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate per-item notes edited from the consumer cart/detail flow.
 */
class UpdateCartNotesRequest extends FormRequest
{
    /**
     * Public consumer cart actions do not require authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Notes are optional because clearing a note is a valid edit.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id_menu' => ['required', 'integer'],
            'cart_key' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
