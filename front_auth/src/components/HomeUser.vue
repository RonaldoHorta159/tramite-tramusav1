<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'
import Button from 'primevue/button'
import Avatar from 'primevue/avatar'
import Menu from 'primevue/menu'
import { useRouter } from 'vue-router'

const toast = useToast()
const user = ref<{ name?: string; avatar?: string } | null>(null)
const logged = ref(false)
const menu = ref()
const router = useRouter()

const logout = async () => {
  try {
    await apiClient.post('/v1/auth/logout')
  } catch { } finally {
    localStorage.clear()
    window.location.href = '/'
  }
}

const userMenuItems = [
  { label: 'Perfil', icon: 'pi pi-user', command: () => { } },
  { label: 'Configuración', icon: 'pi pi-cog', command: () => { } },
  { label: 'Cerrar Sesión', icon: 'pi pi-sign-out', command: logout }
]

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_AUTH_URL,
  headers: { 'Content-Type': 'application/json' }
})

apiClient.interceptors.request.use(cfg => {
  const token = localStorage.getItem('token')
  const type = localStorage.getItem('token_type')
  if (token && type) cfg.headers.Authorization = `${type} ${token}`
  return cfg
})

const getUserProfile = async () => {
  try {
    const { data } = await apiClient.get('/v1/auth/me')
    localStorage.setItem('user', JSON.stringify(data))
    user.value = data
    logged.value = true
  } catch {
    localStorage.clear()
    window.location.href = '/'
  }
}

onMounted(() => {
  if (localStorage.getItem('loginSuccess') === 'true') {
    toast.add({
      severity: 'success',
      summary: '¡Login exitoso!',
      detail: 'Bienvenido de nuevo',
      life: 3000
    })
    localStorage.removeItem('loginSuccess')
  }
  const saved = localStorage.getItem('user')
  if (saved) {
    user.value = JSON.parse(saved)
    logged.value = true
  } else {
    getUserProfile()
  }
})

const toggleUserMenu = (event: Event) => {
  menu.value.toggle(event)
}

// Funciones para los botones del menú vertical
const goToDashboard = () => router.push({ name: 'home-index' })
const goToTramites = () => router.push({ name: 'home-tramite' })
const goToRecibir = () => router.push({ name: 'home-recibir' })
const goToOficinas = () => router.push({ name: 'home-oficinas' })
const goToUsuarios = () => router.push({ name: 'home-usuarios' })
</script>


<!-- dark:bg-black para obcsuro en cada parte que yo quiera -->
<template>
  <div class="flex h-screen bg-[rgb(237,237,237)] ">
    <!-- Menú Vertical -->
    <div
      class="fixed top-0 mt-4 left-4 h-[calc(100vh-2rem)] w-20 flex flex-col items-center justify-between bg-gray-50 rounded-lg p-4  ">
      <div class="flex flex-col items-center gap-4">
        <img src="@/assets/images/logo-tramusa.png" class="border-2 border-gray-800 rounded-lg w-12 h-12 mb-15"
          alt="Logo Tramusa" />
        <Button v-tooltip="'Dashboard'" icon="pi pi-home" text plain size="large" @click="goToDashboard" />
        <Button v-tooltip="'Tramitar'" icon="pi pi-upload" text plain size="large" @click="goToTramites" />
        <Button v-tooltip="'Recibir'" icon="pi pi-download" text plain size="large" @click="goToRecibir" />
        <Button v-tooltip="'Oficinas'" icon="pi pi-building" text plain size="large" @click="goToOficinas" />
        <Button v-tooltip="'Usuarios'" icon="pi pi-users" text plain size="large" @click="goToUsuarios" />
      </div>
      <div class="flex flex-col items-center gap-4">
        <hr class="w-12 border-t border-gray-900" />
        <Avatar v-tooltip="'Perfil'" image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
          style="width: 48px; height: 48px; cursor: pointer;" @click="toggleUserMenu" />
        <Menu ref="menu" :model="userMenuItems" :popup="true" id="overlay_menu" />
      </div>
    </div>
    <!-- Contenido Principal -->
    <div class="flex-1 flex flex-col overflow-hidden ml-24">
      <main class="flex-1 overflow-auto p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
