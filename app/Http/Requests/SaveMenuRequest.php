<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate admin menu create/update forms without changing their existing rules.
 */
class SaveMenuRequest extends FormRequest
{
    /**
     * Admin route middleware handles authorization before this request runs.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Image is required on create and optional on update, matching the old controller rules.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        $imageRule = $this->isMethod('post')
            ? 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
            : 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120';

        return [
            'jenis_menu' => 'required|in:Makanan,Minuman',
            'nama_menu' => 'required|string|max:255',
            'id_kategori' => 'nullable|array',
            'id_kategori.*' => 'exists:kategori_menu,id_kategori',
            'harga' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'deskripsi' => 'required|string|max:500',
            'komposisi' => 'nullable|string|max:500',
            'is_pedas' => 'nullable|boolean',
            'tipe_minuman' => 'required_if:jenis_menu,Minuman|nullable|in:Panas,Dingin,Keduanya',
            'gambar_menu' => $imageRule,
        ];
    }
}
