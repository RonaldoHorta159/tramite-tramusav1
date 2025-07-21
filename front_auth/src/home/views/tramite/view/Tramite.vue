<template>
  <div class=" bg-white p-4 rounded-md border border-gray-200">
    <div class="card">
      <div>
        <ModalDialog />
        <TablaDatos :customers="tramites" :loading="loading" class="mt-5" />
      </div>
      <div>

      </div>
    </div>
  </div>

</template>

<script setup>
import ModalDialog from "../components/ModalDialog.vue";
import { ref, onMounted } from 'vue';
import TablaDatos from '../components/TablaDatos.vue';
import backendApi from '@/services/backendApi';
import { useToast } from 'primevue/usetoast';// <-- 1. Importamos nuestra API

// 2. Creamos una referencia para guardar los datos reales
const tramites = ref([]);
const loading = ref(true);
const toast = useToast(); // <--- Añade esto


// 3. Usamos onMounted para ejecutar el código cuando el componente se monta
onMounted(async () => {
  try {
    const response = await backendApi.get('/v1/auth/seguimientos');
    tramites.value = response.data;
  } catch (error) {
    console.error("Error al cargar los trámites:", error);
    // ✅ ¡Añade esto para notificar al usuario!
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar los datos de los trámites. Por favor, inténtalo de nuevo más tarde.',
      life: 5000
    });
  } finally {
    loading.value = false;
  }
});
</script>
