<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJadwalRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'periode_tahun_id'=> 'required',
            'hari_jadwal' => 'required',
            'jenis_jadwal' => 'required',
            'waktu_mulai'=> 'required',
            'waktu_selesai'=> 'required',
            'makul_id'=> 'required',
            'status'=> 'required',
            'prodi_id'=> 'required',
            'kelas'=> 'required',
            'ruangan_id'=> ['required',
            Rule::unique('jadwals','ruangan_id')
            ->where('periode_tahun_id', $this->input('periode_tahun_id'))
            ->where('hari_jadwal', $this->input('hari_jadwal'))
            ->where('waktu_mulai', $this->input('waktu_mulai'))],
            'dosen_1'=> ['required',
            Rule::unique('jadwals','dosen_1')
            ->where('periode_tahun_id', $this->input('periode_tahun_id'))
            ->where('hari_jadwal', $this->input('hari_jadwal'))
            ->where('waktu_mulai', $this->input('waktu_mulai'))
            ->where('dosen_1', $this->input('dosen_1'))
            ->where('dosen_2', $this->input('dosen_1'))],
            'dosen_2'=> ['required',
            Rule::unique('jadwals','dosen_2')
            ->where('periode_tahun_id', $this->input('periode_tahun_id'))
            ->where('hari_jadwal', $this->input('hari_jadwal'))
            ->where('waktu_mulai', $this->input('waktu_mulai'))
            ->where('dosen_1', $this->input('dosen_2'))
            ->where('dosen_2', $this->input('dosen_2'))],
            'laboran_id'=> ['required',
            Rule::unique('jadwals','laboran_id')
            ->where('periode_tahun_id', $this->input('periode_tahun_id'))
            ->where('hari_jadwal', $this->input('hari_jadwal'))
            ->where('waktu_mulai', $this->input('waktu_mulai'))],
        ];
    }
    public function messages()
    {
        return [
            'dosen_1.unique' => 'dosen sudah terjadwal di sesi ini.',
            'dosen_2.unique' => 'dosen sudah terjadwal di sesi ini.',
            'laboran_id.unique' => 'laboran sudah terjadwal di sesi ini.',
            'ruangan_id.unique' => 'ruangan ini sudah digunakan.',
        ];
    }
}
