<script setup>
import { ref, onMounted } from 'vue';
import TablaDatosRecibir from '../components/TablaDatosRecibir.vue';
import RecepcionarModal from '../../../../components/RecepcionarModal.vue';
import { seguimientoService } from '../../../../services/seguimientoService';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const seguimientos = ref([]);
const isLoading = ref(true);

// 2. AÑADIMOS ESTADO PARA EL NUEVO MODAL
const recepcionarModalVisible = ref(false);

const fetchData = async () => {
  isLoading.value = true;
  try {
    seguimientos.value = await seguimientoService.fetchSeguimientosPorRecibir();
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los trámites.', life: 3000 });
  } finally {
    isLoading.value = false;
  }
};

// 3. FUNCIÓN PARA ABRIR EL MODAL
const abrirModalRecepcion = () => {
  if (seguimientos.value.length === 0) {
    toast.add({ severity: 'info', summary: 'Información', detail: 'No hay trámites pendientes para recepcionar.', life: 3000 });
    return;
  }
  recepcionarModalVisible.value = true;
};

onMounted(fetchData);
</script>
<template>
  <div class="card">
    <TablaDatosRecibir :tramites="seguimientos" :loading="isLoading" @reload="fetchData"
      @abrir-recepcionar="abrirModalRecepcion" />

    <RecepcionarModal v-model:visible="recepcionarModalVisible" :tramites="seguimientos" @success="fetchData" />
  </div>
</template>
