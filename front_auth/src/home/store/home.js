import { defineStore } from 'pinia'

export const homeStore = defineStore('home', {
  state: () => ({
    user: null,
    isAuthenticated: false,
    loading: false,
  }),

  actions: {
    async getUserProfile() {
      try {
        const { data } = await apiClient.get('/v1/auth/me')
        localStorage.setItem('user', JSON.stringify(data))
        user.value = data
        logged.value = true
      } catch {
        localStorage.clear()
        window.location.href = '/'
      }
    },
  },

  getters: {},
})
