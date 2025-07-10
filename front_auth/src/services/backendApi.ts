// src/services/backendApi.ts

import axios, { type AxiosInstance } from 'axios'

const backendApi: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_AUTH_URL,
})

// ✅ Usar un interceptor para añadir el token en cada petición
backendApi.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default backendApi
