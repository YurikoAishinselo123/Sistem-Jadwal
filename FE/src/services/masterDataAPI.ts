import apiClient from './apiClient'

export default class MasterDataAPI {
  static async getAll(resource: string) {
    return apiClient.get(`/${resource}`)
  }

  static async getById(resource: string, id: number | string) {
    return apiClient.get(`/${resource}/${id}`)
  }

  static async create(resource: string, payload: any) {
    return apiClient.post(`/${resource}`, payload)
  }

  static async update(resource: string, id: number | string, payload: any) {
    return apiClient.put(`/${resource}/${id}`, payload)
  }

  static async delete(resource: string, id: number | string) {
    return apiClient.delete(`/${resource}/${id}`)
  }
}
