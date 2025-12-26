import apiClient from './apiClient'

export const dashboardAPI = {
  getJadwal: () => apiClient.get('/jadwal'),
  getLaboran: () => apiClient.get('/laboran'),
  getProdi: () => apiClient.get('/prodi'),
  getMakul: () => apiClient.get('/makul'),
  getWaktuPerkuliahan: () => apiClient.get('/waktu'),
  getPeriode: () => apiClient.get('/periode'),
  getFilter: () => apiClient.get('/filter'),
  getJadwalFormData: () => apiClient.get('/datainput'),

  getJadwalById: (id: number) => apiClient.get(`/jadwal/${id}`),
  createJadwal: (data: any) => apiClient.post(`/jadwal`, data),
  updateJadwal: (id: number, data: any) => apiClient.put(`/jadwal/${id}`, data),
  deleteJadwal: (id: number) => apiClient.delete(`/jadwal/${id}`),
  deleteSelectedJadwal: (ids: number[]) =>
    apiClient.delete('/jadwal/', {
      data: { ids },
    }),
}
