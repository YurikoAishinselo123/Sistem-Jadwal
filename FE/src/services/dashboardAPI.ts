import apiClient from './apiClient'

export const dashboardAPI = {
  getJadwal: () => apiClient.get('/jadwal'),
  getJadwalById: (id: number) => apiClient.get(`/jadwal/${id}`),
  createJadwal: (data: any) => apiClient.post(`/jadwal`, data),
  updateJadwal: (id: number, data: any) => apiClient.put(`/jadwal/${id}`, data),
  deleteJadwal: (id: number) => apiClient.delete(`/jadwal/${id}`),
}
