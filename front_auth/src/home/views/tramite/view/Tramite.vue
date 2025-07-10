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
import backendApi from '@/services/backendApi'; // <-- 1. Importamos nuestra API

// 2. Creamos una referencia para guardar los datos reales
const tramites = ref([]);
const loading = ref(true);

// 3. Usamos onMounted para ejecutar el código cuando el componente se monta
onMounted(async () => {
  try {
    // Hacemos la llamada GET a nuestro endpoint de Laravel
    const response = await backendApi.get('/v1/auth/seguimientos');
    tramites.value = response.data; // Guardamos los datos en nuestra referencia
  } catch (error) {
    console.error("Error al cargar los trámites:", error);
    // Aquí podrías mostrar una notificación de error al usuario
  } finally {
    loading.value = false; // Dejamos de mostrar el estado de carga
  }
});
</script>
