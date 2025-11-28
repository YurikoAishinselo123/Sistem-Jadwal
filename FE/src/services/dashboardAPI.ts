import axios from 'axios'
const API_BASE_URL = 'http://localhost:3000/api/auth'

export const dashboardAPI = {
  getJadwal: () => axios.get(`${API_BASE_URL}/jadwal`),
  getJadwalById: (id: number) => axios.get(`${API_BASE_URL}/jadwal/${id}`),
  createJadwal: (data: any) => axios.post(`${API_BASE_URL}/jadwal`, data),
  updateJadwal: (id: number, data: any) => axios.put(`${API_BASE_URL}/jadwal/${id}`, data),
  deleteJadwal: (id: number) => axios.delete(`${API_BASE_URL}/jadwal/${id}`),
}
