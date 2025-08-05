// src/main.ts
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'

// PrimeVue core
import PrimeVue from 'primevue/config'
import { definePreset } from '@primeuix/themes'
// Importa definePreset para personalizar el tema

// PrimeVue theme (Aura) y estilos de iconos
import Aura from '@primeuix/themes/aura'
// Corrige la importación (era @primeuix, debe ser @primevue)
import 'primeicons/primeicons.css'

// PrimeVue Toast
import ToastService from 'primevue/toastservice'
import Toast from 'primevue/toast'
import Tooltip from 'primevue/tooltip'

// PrimeVue ConfirmDialog
import ConfirmDialog from 'primevue/confirmdialog'
import ConfirmationService from 'primevue/confirmationservice' // <-- AÑADE ESTA LÍNEA

// Define un preset personalizado basado en Aura con color primario slate y botones más oscuros
const MyPreset = definePreset(Aura, {
  semantic: {
    primary: {
      50: '{slate.50}',
      100: '{slate.100}',
      200: '{slate.200}',
      300: '{slate.300}',
      400: '{slate.400}',
      500: '{slate.500}',
      600: '{slate.600}',
      700: '{slate.700}',
      800: '{slate.800}',
      900: '{slate.900}',
      950: '{slate.950}',
    },
  },
  components: {
    button: {
      css: ({ dt }) => `
        .p-button {
          background: ${dt('primary.800')};
        }
        .p-button:hover {
          background: ${dt('primary.900')};
        }
        .p-button:active {
          background: ${dt('primary.950')};
        }
      `,
    },
  },
})

const app = createApp(App)
app.directive('tooltip', Tooltip)

// Usa el preset personalizado en la configuración de PrimeVue
app.use(PrimeVue, {
  theme: { preset: MyPreset, options: { darkModeSelector: false } },
})

// Registra el servicio de Toast ANTES de montar
app.use(ToastService)
// Y registra el componente global de Toast
app.component('Toast', Toast)

// Registra Pinia y PrimeVue, y el servicio de confirmación de PrimeVue
app.use(createPinia())
app.use(ConfirmationService)
app.component('ConfirmDialog', ConfirmDialog)
app.use(ConfirmationService)

app.use(router)
app.use(PrimeVue, { ripple: true })
app.use(ConfirmationService) // <-- AÑADE ESTA LÍNEA
app.use(ToastService)

app.mount('#app')
