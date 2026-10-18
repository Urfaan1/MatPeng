<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')
                    ->where(function ($query) {
                        return $query->where('user_id', Auth::id())
                                     ->where('type', $this->input('type'));
                    })
                    ->ignore($categoryId),
            ],
            'type' => ['required', 'string', 'in:income,expense'],
        ];
    }

    /**
     * Dapatkan pesan kesalahan validasi khusus dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'name.unique' => 'Kategori dengan nama dan jenis transaksi ini sudah ada pada akun Anda.',
            'type.required' => 'Jenis kategori wajib dipilih.',
            'type.in' => 'Jenis kategori harus berupa pemasukan atau pengeluaran.',
        ];
    }
}
