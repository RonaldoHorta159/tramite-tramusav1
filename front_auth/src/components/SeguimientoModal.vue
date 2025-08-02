<script setup>
import { ref, watch } from 'vue';
import { seguimientoService } from '@/services/seguimientoService';
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';


const props = defineProps({
  visible: Boolean,
  documentoId: Number,
})

const emit = defineEmits(['close']);

const historial = ref([]);
const loading = ref(false);
const primerSeguimiento = ref(null);

const formatDate = (value) => new Date(value).toLocaleString('es-ES');

const fetchHistorial = async (id) => {
  if (!id) return;
  loading.value = true;
  historial.value = await seguimientoService.fetchHistorial(id);
  primerSeguimiento.value = historial.value.length > 0 ? historial.value[0] : null;
  loading.value = false;
};

watch(() => props.documentoId, (newId) => {
  if (newId) fetchHistorial(newId);
});

</script>

<template>
  <Dialog :visible="props.visible" @update:visible="emit('close')" modal header="Historial del Trámite"
    :style="{ width: '75vw' }">
    <div v-if="primerSeguimiento" class="mb-4">
      <p><strong>Código Único:</strong>
        <Tag severity="info" :value="primerSeguimiento.CU"></Tag>
      </p>
      <p><strong>Asunto:</strong> {{ primerSeguimiento.asunto }}</p>
    </div>
    <DataTable :value="historial" :loading="loading" class="p-datatable-sm">
      <Column header="Fecha" field="created_at">
        <template #body="{ data }">{{ formatDate(data.created_at) }}</template>
      </Column>
      <Column header="Oficina Origen" field="oficinaOrigen.name"></Column>
      <Column header="Oficina Destino" field="oficinaDestino.name"></Column>
      <Column header="Estado" field="estado"></Column>
      <Column header="Proveído" field="proveido"></Column>
    </DataTable>
    <template #footer>
      <Button label="Cerrar" icon="pi pi-times" @click="emit('close')" severity="secondary" />
    </template>
  </Dialog>
</template>
