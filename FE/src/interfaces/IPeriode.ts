export interface IPeriode {
  id?: number | string
  periode: string
  status: 'Aktif' | 'Selesai'
  tanggalMulai: string
  tanggalSelesai: string | null
}
