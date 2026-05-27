<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate quantity changes for an item already stored in the session cart.
 */
class UpdateCartItemRequest extends FormRequest
{
    /**
     * Public consumer cart actions do not require authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Quantity zero intentionally means "remove this cart line".
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id_menu' => ['required', 'integer'],
            'cart_key' => ['nullable', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }
}
