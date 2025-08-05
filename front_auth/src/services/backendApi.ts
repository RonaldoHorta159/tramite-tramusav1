// front_auth/src/services/backendApi.ts

import axios from 'axios'

// 1. Creamos una instancia de Axios con la URL base de tu API
const axiosInstance = axios.create({
  baseURL: 'http://auth_back.test/api/v1/auth', // Asegúrate de que esta sea tu URL base
})

// 2. Usamos un interceptor para modificar las peticiones ANTES de que se envíen
axiosInstance.interceptors.request.use(
  (config) => {
    // Obtenemos el token guardado en el Local Storage
    const token = localStorage.getItem('token')

    // Si el token existe, lo añadimos a las cabeceras de la petición
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    return config // Devolvemos la configuración modificada
  },
  (error) => {
    // Hacemos algo con el error de la petición
    return Promise.reject(error)
  },
)

// Opcional: Puedes añadir un interceptor de respuesta para manejar errores 401 globalmente
axiosInstance.interceptors.response.use(
  (response) => response, // Si la respuesta es exitosa, simplemente la devolvemos
  (error) => {
    // Si la respuesta es un 401, podría significar que el token expiró.
    // Aquí podrías redirigir al login.
    if (error.response && error.response.status === 401) {
      console.error('Token no autorizado o expirado. Redirigiendo al login...')
      localStorage.removeItem('token') // Limpiamos el token viejo
      // window.location.href = '/login'; // Descomenta para redirigir automáticamente
    }
    return Promise.reject(error)
  },
)

export default axiosInstance
