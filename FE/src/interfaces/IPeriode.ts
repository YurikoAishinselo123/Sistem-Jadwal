export interface IPeriode {
  id?: number | string
  namaPeriode: string
  tahun: number
  status: 'Aktif' | 'Selesai'
  tanggalMulai: string
  tanggalSelesai: string | null
}
