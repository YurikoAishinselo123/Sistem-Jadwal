import apiClient from './apiClient'

export const bebanruangAPI = {
  getRuang: () => apiClient.get('/ruangan'),
  getRuangbyid: (id: number) => apiClient.get(`/ruangan/${id}`),
}
