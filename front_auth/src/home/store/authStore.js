// src/store/authStore.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // Estado
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  // Getters (Propiedades computadas)
  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.rol === 'admin')

  // Acciones
  function setUser(userData) {
    user.value = userData
  }

  function setToken(tokenValue) {
    token.value = tokenValue
    localStorage.setItem('token', tokenValue)
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  return { user, token, isLoggedIn, isAdmin, setUser, setToken, logout }
})
