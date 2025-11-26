import axios from 'axios'

const API_BASE_URL = 'http://localhost:3000/api/auth'

export const authenticationAPI = {
  signup: async (data: { email: string; username: string; password: string }) => {
    return axios.post(`${API_BASE_URL}/signup`, data)
  },

  login: async (data: { username: string; password: string }) => {
    return axios.post(`${API_BASE_URL}/login`, data)
  },
}
