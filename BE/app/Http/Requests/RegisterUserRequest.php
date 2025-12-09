<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required'//
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => 'Nama tidak bisa kosong.',
            'name.max' => 'Nama melebihi batas karakter.',
            'nama.unique' => 'Nama sudah terdaftar.',
            'email.required' => 'Email tidak bisa kosong.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password tidak bisa kosong.',
        ];
    }
}
