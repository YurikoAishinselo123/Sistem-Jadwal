<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJadwalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hari_jadwal' => 'required',
            'waktu_mulai'=> 'required',
            'sesi'=> 'required',
            'tahun_ajaran'=> 'required',
            'id_prodi'=> ['required'],
            'id_makul'=> ['required'],
            'id_dosen'=> ['required',
            Rule::unique('jadwals','id_dosen')
            ->where('tahun_ajaran', $this->input('tahun_ajaran'))
            ->where('hari_jadwal', $this->input('hari_jadwal'))
            ->where('waktu_mulai', $this->input('waktu_mulai'))],
            'id_laboran'=> ['required',
            Rule::unique('jadwals','id_laboran')
            ->where('tahun_ajaran', $this->input('tahun_ajaran'))
            ->where('hari_jadwal', $this->input('hari_jadwal'))
            ->where('waktu_mulai', $this->input('waktu_mulai'))],
            'id_ruangan'=> ['required',
            Rule::unique('jadwals','id_ruangan')
            ->where('tahun_ajaran', $this->input('tahun_ajaran'))
            ->where('hari_jadwal', $this->input('hari_jadwal'))
            ->where('waktu_mulai', $this->input('waktu_mulai'))]
        ];
    }
    public function messages()
    {
        return [
            'id_dosen.unique' => 'dosen sudah terjadwal di sesi ini.',
            'id_laboran.unique' => 'laboran sudah terjadwal di sesi ini.',
            'id_ruangan.unique' => 'ruangan ini sudah digunakan.',
        ];
    }
}