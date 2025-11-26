import axios from 'axios'

const apiClient = axios.create({
  baseURL: 'http://localhost:8000', // change according to your backend URL
  withCredentials: true, // enable cookies (Laravel Sanctum)
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

export default apiClient
