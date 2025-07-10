import axios from 'axios'

const backendApi = axios.create({
  baseURL: import.meta.env.VITE_API_AUTH_URL,
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`,
  },
})

export default backendApi
