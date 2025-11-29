import axios from 'axios'

const API_URL = 'http://localhost:8000/api' // adjust to your backend URL

export default class MasterDataAPI {
  static async getAll(resource: string) {
    return axios.get(`${API_URL}/${resource}`)
  }

  static async getById(resource: string, id: number | string) {
    return axios.get(`${API_URL}/${resource}/${id}`)
  }

  static async create(resource: string, payload: any) {
    return axios.post(`${API_URL}/${resource}`, payload)
  }

  static async update(resource: string, id: number | string, payload: any) {
    return axios.put(`${API_URL}/${resource}/${id}`, payload)
  }

  static async delete(resource: string, id: number | string) {
    return axios.delete(`${API_URL}/${resource}/${id}`)
  }
}
