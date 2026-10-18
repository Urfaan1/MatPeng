<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk membuat request ini.
     */
    public function authorize(): bool
    {
        $transaction = $this->route('transaction');
        return Auth::check() && $transaction && $transaction->user_id === Auth::id();
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where(function ($query) {
                        $query->where('user_id', Auth::id())
                              ->where('type', $this->input('type'));
                    }),
            ],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Dapatkan pesan kesalahan khusus untuk atribut validator yang ditentukan.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Jenis transaksi wajib dipilih.',
            'type.in' => 'Jenis transaksi harus berupa pemasukan atau pengeluaran.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.numeric' => 'Nominal transaksi harus berupa angka.',
            'amount.min' => 'Nominal transaksi harus lebih besar dari nol (0).',
            'amount.max' => 'Nominal transaksi melebihi batas maksimum yang diizinkan.',
            'category_id.required' => 'Kategori transaksi wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid atau tidak cocok dengan jenis transaksi yang dipilih.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal tidak valid.',
            'description.max' => 'Deskripsi tidak boleh melebihi 1000 karakter.',
        ];
    }
}
