<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama_depan' => 'required|string|max:255',
            'nama_belakang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kode_pos' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'hp' => 'required|string|max:100',
            'kelurahan' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama_depan.required' => 'First name is required.',
            'nama_depan.max' => 'First name cannot exceed 255 characters.',
            'nama_belakang.required' => 'Last name is required.',
            'nama_belakang.max' => 'Last name cannot exceed 255 characters.',
            'alamat.required' => 'Address is required.',
            'kode_pos.required' => 'Postal code is required.',
            'kode_pos.max' => 'Postal code cannot exceed 100 characters.',
            'kecamatan.required' => 'District is required.',
            'kecamatan.max' => 'District cannot exceed 100 characters.',
            'provinsi.required' => 'Province is required.',
            'provinsi.max' => 'Province cannot exceed 100 characters.',
            'hp.required' => 'Phone number is required.',
            'hp.max' => 'Phone number cannot exceed 100 characters.',
            'kelurahan.required' => 'Sub-district is required.',
            'kelurahan.max' => 'Sub-district cannot exceed 100 characters.',
            'kota.required' => 'City is required.',
            'kota.max' => 'City cannot exceed 100 characters.',
        ];
    }
}
