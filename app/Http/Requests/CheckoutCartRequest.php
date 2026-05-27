<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the final consumer checkout form before writing Pesanan records.
 */
class CheckoutCartRequest extends FormRequest
{
    /**
     * Public consumer checkout does not require authentication.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preserve the existing consumer name and optional order-note constraints.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nama_konsumen' => ['required', 'string', 'max:255'],
            'catatan_pesanan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
