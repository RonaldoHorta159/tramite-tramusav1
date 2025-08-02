<template>
  <div class="bg-white p-4 rounded-md border border-gray-200">
    <div class="card">
      <h2 class="text-xl font-semibold mb-4">Bandeja de Entrada: Trámites por Recibir</h2>
      <ObservarModal :visible="isObservarModalVisible" :tramite="tramiteSeleccionado"
        @close="isObservarModalVisible = false" @tramite-observado="cargarTramites" />
      <TablaDatosRecibir :tramites="tramitesPorRecibir" :loading="loading" class="mt-5"
        @observar-tramite="handleObservarTramite" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import TablaDatosRecibir from '../components/TablaDatosRecibir.vue';
import { seguimientoService } from '@/services/seguimientoService';
import { useToast } from 'primevue/usetoast';
import ObservarModal from '@/components/ObservarModal.vue';

// 1. Referencias para los datos y el estado de carga
const tramitesPorRecibir = ref([]);
const loading = ref(true);
const toast = useToast();

const isObservarModalVisible = ref(false);
const tramiteSeleccionado = ref(null);

const handleObservarTramite = (tramite) => {
  tramiteSeleccionado.value = tramite;
  isObservarModalVisible.value = true;
};

// 2. Función para cargar los datos desde el servicio
const cargarTramites = async () => {
  loading.value = true;
  try {
    tramitesPorRecibir.value = await seguimientoService.fetchSeguimientosPorRecibir();
  } catch (error) {
    console.error("Error al cargar los trámites por recibir:", error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar los trámites de la bandeja de entrada.',
      life: 5000
    });
  } finally {
    loading.value = false;
  }
};

// 3. Llama a la función para cargar datos cuando el componente se monta
onMounted(cargarTramites);
</script>
