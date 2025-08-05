<script setup>
import { ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { seguimientoService } from '../services/seguimientoService';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const toast = useToast();

const props = defineProps({
  visible: Boolean,
  tramites: Array // Recibimos la lista de trámites pendientes
});

const emit = defineEmits(['update:visible', 'success']);

const visibleDialog = ref(props.visible);
const selectedTramites = ref([]); // Aquí guardaremos los trámites seleccionados
const isLoading = ref(false);

watch(() => props.visible, (newVal) => {
  visibleDialog.value = newVal;
  if (!newVal) {
    // Limpiamos la selección cuando el modal se cierra
    selectedTramites.value = [];
  }
});

watch(visibleDialog, (newVal) => {
  if (!newVal) {
    emit('update:visible', false);
  }
});

const guardarRecepcion = async () => {
  if (selectedTramites.value.length === 0) {
    toast.add({ severity: 'warn', summary: 'Sin Selección', detail: 'Debe seleccionar al menos un trámite para recibir.', life: 3000 });
    return;
  }
  isLoading.value = true;
  try {
    // Extraemos solo los IDs de los trámites seleccionados
    const idsParaRecibir = selectedTramites.value.map(tramite => tramite.id);

    // Llamamos a nuestra nueva función del servicio
    await seguimientoService.recibirTramitesEnLote(idsParaRecibir);

    toast.add({ severity: 'success', summary: 'Éxito', detail: 'Trámites recibidos correctamente.', life: 3000 });
    emit('success'); // Avisamos al padre que la operación fue exitosa para que recargue la tabla
    visibleDialog.value = false; // Cerramos el modal
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron recibir los trámites.', life: 3000 });
    console.error("Error al recibir en lote:", error);
  } finally {
    isLoading.value = false;
  }
};

</script>

<template>
  <Dialog v-model:visible="visibleDialog" header="Recepcionar Documentos Pendientes" :modal="true" style="width: 70vw;">
    <p>Seleccione los documentos que desea recepcionar y luego haga clic en "Guardar".</p>

    <DataTable :value="props.tramites" v-model:selection="selectedTramites" dataKey="id" :loading="isLoading" paginator
      :rows="10" tableStyle="min-width: 50rem">
      <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
      <Column field="cu" header="Código Único"></Column>
      <Column field="origen_oficina" header="Origen"></Column>
      <Column field="asunto" header="Asunto" style="min-width: 16rem;"></Column>
      <Column field="num_folios" header="Folios"></Column>
    </DataTable>

    <template #footer>
      <Button label="Cancelar" icon="pi pi-times" @click="visibleDialog = false" class="p-button-text" />
      <Button label="Guardar Recepción" icon="pi pi-save" @click="guardarRecepcion" :loading="isLoading" />
    </template>
  </Dialog>
</template>
