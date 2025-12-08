import apiClient from './apiClient'

export const bebandosenAPI = {
  getDosen: () => apiClient.get('/dosen'),
  getDosenbyid: (id: number) => apiClient.get(`/dosen/${id}`),
}
