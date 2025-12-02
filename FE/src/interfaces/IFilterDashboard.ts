export interface IFilterDashboard {
  periodeTahunAjaran: string
  hari: string
  programStudi: string
  mataKuliah: string
  jenisJadwal: string
  laboran: string
  waktuPerkuliahan: string
}

export interface IJadwalResponse {
  id: number
  nama_dosen_1?: string
  nama_dosen_2?: string
  nama_laboran?: string
  nama_prodi?: string
  nama_makul?: string
  hari_jadwal?: string
  waktu_mulai?: string
  waktu_selesai?: string
  kode_ruangan?: string
  kelas?: string
  status?: string
  jenis_jadwal?: string
  nama_periode?: string
  tahun_periode?: string
  periodeTahunAjaran?: string
}
