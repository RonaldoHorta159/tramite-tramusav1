<template>
  <div class="bg-white p-4 rounded-md border border-gray-200">
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
import { seguimientoService } from '@/services/seguimientoService';
import { useToast } from 'primevue/usetoast';

const tramites = ref([]);
const loading = ref(true);
const toast = useToast();

onMounted(async () => {
  try {
    tramites.value = await seguimientoService.fetchSeguimientos();
  } catch (error) {
    console.error("Error al cargar los trámites:", error);
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
