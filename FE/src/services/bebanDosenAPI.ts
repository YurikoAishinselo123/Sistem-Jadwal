import apiClient from '@/services/apiClient'

export const bebanDosenAPI = {
  getDosen: () => apiClient.get('/dosen'),
  getDosenById: (id: number) => apiClient.get(`/dosen/${id}`),
}
