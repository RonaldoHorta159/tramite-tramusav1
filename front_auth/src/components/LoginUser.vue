<!-- loginUser.vue -->
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'
import InputGroup from 'primevue/inputgroup'
import InputGroupAddon from 'primevue/inputgroupaddon'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import FloatLabel from 'primevue/floatlabel'

import heroImage from '../assets/images/fondo-login.webp'
import logo from '../assets/images/logo-tramusa.png'
import { useAuthStore } from '@/home/store/authStore'

const identifier = ref('')
const password = ref('')

const isLoading = ref(false)
const passwordVisible = ref(false)

const toast = useToast()
const router = useRouter()

const loginUrl = import.meta.env.VITE_API_AUTH_URL + '/v1/auth/login'

const authStore = useAuthStore();

const login = async () => {
  isLoading.value = true

  try {
    const { data } = await axios.post(loginUrl, {
      identifier: identifier.value,
      password: password.value
    })

    localStorage.setItem('token', data.access_token)
    localStorage.setItem('token_type', data.token_type)
    localStorage.setItem('loginSuccess', 'true')

    router.push('/home')
  }
  catch (err) {
    const msg = err.response?.data.error || err.message || 'Error de conexión'

    toast.add({
      severity: 'error',
      summary: 'Error al iniciar sesión',
      detail: msg,
      life: 5000
    })
  }
  finally {
    isLoading.value = false
  }
}

const togglePasswordVisibility = () => {
  passwordVisible.value = !passwordVisible.value
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-4xl w-full bg-white rounded-sm shadow-xl/30 overflow-hidden grid grid-cols-1 md:grid-cols-2">

      <div class="hidden md:block h-full">
        <img :src="heroImage" alt="Imagen de fondo" class="w-full h-full object-cover p-0.5" />
      </div>

      <div class="p-8 md:p-12">
        <img :src="logo" alt="Logo Tramusa" class="mx-auto mb-4 w-40 h-40 object-contain" />

        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
          Bienvenido a trámite documentario
        </h1>

        <form @submit.prevent="login" class="space-y-6">
          <InputGroup>
            <InputGroupAddon class="bg-transparent border-0">
              <i class="pi pi-user text-gray-400"></i>
            </InputGroupAddon>
            <FloatLabel>
              <InputText v-model="identifier" type="text" class="p-inputtext-lg" placeholder=" "
                style="border: 1px solid #E5E7EB; background: transparent; color: #1F2937;" />
              <label class="text-gray-500">Correo o DNI</label>
            </FloatLabel>
          </InputGroup>

          <InputGroup class="relative">
            <InputGroupAddon class="bg-transparent border-0">
              <i class="pi pi-lock text-gray-400"></i>
            </InputGroupAddon>
            <FloatLabel>
              <InputText v-model="password" :type="passwordVisible ? 'text' : 'password'" class="p-inputtext-lg pr-10"
                placeholder=" " style="border: 1px solid #E5E7EB; background: transparent; color: #1F2937;" />
              <label class="text-gray-500">Contraseña</label>
            </FloatLabel>
            <i :class="passwordVisible ? 'pi pi-eye-slash' : 'pi pi-eye'"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-400"
              @click="togglePasswordVisibility" />
          </InputGroup>

          <Button label="Ingresar" type="submit" class="p-button p-button-lg w-full" :disabled="isLoading"
            style="background-color: rgb(154, 3, 3); color: #FFF; border-radius: 0.5rem;" />
        </form>
      </div>

    </div>
  </div>
</template>

<style scoped>
.p-inputtext-lg {
  width: 100%;
  height: 3rem;
  padding: 0.75rem;
  font-size: 1rem;
  border-radius: 0.375rem;
}

.p-inputgroup-addon {
  width: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-checkbox {
  height: 1rem;
  width: 1rem;
}
</style>
