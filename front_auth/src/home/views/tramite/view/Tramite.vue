<template>
  <div class="bg-white p-4 rounded-md border border-gray-200">
    <div class="card">
      <ModalDialog :visible="isModalVisible" @update:visible="isModalVisible = $event"
        @tramite-creado="refreshTramites" />
      <TablaDatos :customers="tramites" :loading="loading" class="mt-5" @open-new-modal="handleOpenModal" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import TablaDatos from '../components/TablaDatos.vue';
import ModalDialog from "../components/ModalDialog.vue";
import { seguimientoService } from '@/services/seguimientoService';
import { useToast } from 'primevue/usetoast';

const tramites = ref([]);
const loading = ref(true);
const toast = useToast();
const isModalVisible = ref(false); // <--- 1. Variable para controlar el modal

// --- 2. Función para abrir el modal ---
const handleOpenModal = () => {
  isModalVisible.value = true;
};

// --- 3. Función para refrescar la lista de trámites ---
const refreshTramites = async () => {
  loading.value = true;
  try {
    tramites.value = await seguimientoService.fetchSeguimientos();
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo refrescar la lista de trámites.', life: 3000 });
  } finally {
    loading.value = false;
  }
};

// Carga inicial de datos
onMounted(refreshTramites);
</script>
