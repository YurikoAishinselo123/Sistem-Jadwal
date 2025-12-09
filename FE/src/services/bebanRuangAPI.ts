import apiClient from '@/services/apiClient'

export const bebanRuangAPI = {
  getRuangan: () => apiClient.get('/ruangan'),
  getRuanganById: (id: number) => apiClient.get(`/ruangan/${id}`),
}
