<?php

namespace Modules\Pendaftar\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nomor_registrasi' => ['required'],
            'kategori' => ['required'],
            'nama' => ['required'],
            'tempat_lahir' => ['required'],
            'tanggal_lahir' => ['required'],
            'jenis_kelamin' => ['required'],
            'pendidikan' => ['required'],
            'alamat' => ['required'],
            'nomor_wa' => ['required'],
            'email' => ['required'],
            'ktp' => ['required'],
            'foto' => ['required'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
