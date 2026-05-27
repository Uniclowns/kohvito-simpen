<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a consumer request to add a customized menu item to the session cart.
 */
class StoreCartItemRequest extends FormRequest
{
    /**
     * Public consumer cart actions do not require authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Keep the original cart add constraints from KeranjangKonsumenController.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id_menu' => ['required', 'integer', 'exists:menu,id_menu'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:99'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'harga_tambahan' => ['nullable', 'integer', 'min:0', 'max:500000'],
        ];
    }
}
