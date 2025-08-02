<template>
  <div class="bg-white p-4 rounded-md border border-gray-200">
    <div class="card">
      <ModalDialog :visible="isModalVisible" @update:visible="isModalVisible = $event"
        @tramite-creado="refreshTramites" />
      <SeguimientoModal :visible="isSeguimientoModalVisible" :documento-id="selectedDocumentoId"
        @close="isSeguimientoModalVisible = false" />
      <TablaDatos :customers="tramites" :loading="loading" class="mt-5" @open-new-modal="handleOpenModal"
        @recibir-tramite="handleRecibirTramite" @ver-seguimiento="handleVerSeguimiento" />
    </div>
  </div>
</template>

<script setup>
import SeguimientoModal from '@/components/SeguimientoModal.vue';
import { ref, onMounted } from 'vue';
import TablaDatos from '../components/TablaDatos.vue';
import ModalDialog from "../components/ModalDialog.vue";
import { seguimientoService } from '@/services/seguimientoService';
import { useToast } from 'primevue/usetoast';

const tramites = ref([]);
const loading = ref(true);
const toast = useToast();
const isModalVisible = ref(false); // <--- 1. Variable para controlar el modal
const isSeguimientoModalVisible = ref(false);
const selectedDocumentoId = ref(null);

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

const handleVerSeguimiento = (tramite) => {
  selectedDocumentoId.value = tramite.documentos_id;
  isSeguimientoModalVisible.value = true;
};

// --- 👇 LÓGICA NUEVA 👇 ---
const handleRecibirTramite = async (tramite) => {
  try {
    // Llama al servicio para actualizar el estado en el backend
    const response = await seguimientoService.recibirTramite(tramite.id);

    // Muestra notificación de éxito
    toast.add({ severity: 'success', summary: 'Éxito', detail: response.message, life: 3000 });

    // Vuelve a cargar la lista de trámites. El que acabamos de recibir ya no aparecerá.
    await refreshTramites();
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'Ocurrió un error al recibir el trámite.';
    toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 5000 });
  }
};
// --- 👆 FIN DE LA LÓGICA NUEVA 👆 ---

// Carga inicial de datos
onMounted(refreshTramites);
</script>
