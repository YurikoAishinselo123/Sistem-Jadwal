import apiClient from '@/services/apiClient'

export const authenticationAPI = {
  signup: (data: { email: string; name: string; password: string }) =>
    apiClient.post('register', data),

  login: (data: { name: string; password: string }) => apiClient.post('login', data),
}
