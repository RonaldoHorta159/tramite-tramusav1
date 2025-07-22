<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

const emit = defineEmits(['open-new-modal']);

const props = defineProps({
  customers: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const formatDate = (value) => {
  if (!value) return '';
  return new Date(value).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

const verPdf = (pdfUrl) => {
  if (pdfUrl) {
    window.open(`http://localhost:8000${pdfUrl}`, '_blank');
  }
};

const openNew = () => emit('open-new-modal');

// Funciones placeholder para las acciones
const verSeguimiento = (tramite) => console.log('Ver Seguimiento:', tramite);
const derivarTramite = (tramite) => console.log('Derivar:', tramite);
const anularTramite = (tramite) => console.log('Anular:', tramite);

</script>

<template>
  <div>
    <Toolbar class="mb-4">
      <template #start>
        <Button label="Nuevo Trámite" icon="pi pi-plus" class="p-button-success" @click="openNew" />
      </template>
    </Toolbar>

    <DataTable :value="props.customers" :loading="props.loading" paginator :rows="10" dataKey="id"
      v-model:filters="filters" :globalFilterFields="['CU', 'asunto', 'documento.tipo_documento']" showGridlines
      responsiveLayout="scroll" class="p-datatable-sm">

      <template #header>
        <div class="flex justify-end">
          <IconField>
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText v-model="filters['global'].value" placeholder="Buscar..." />
          </IconField>
        </div>
      </template>

      <template #empty> No se encontraron resultados. </template>
      <template #loading> Cargando datos, por favor espere... </template>

      <Column field="CU" header="Código Único" sortable />

      <Column header="Opciones" style="min-width: 10rem; text-align: center;">
        <template #body="{ data }">
          <div class="flex gap-2 justify-center">
            <Button icon="pi pi-eye" class="p-button-rounded p-button-info" @click="verSeguimiento(data)"
              v-tooltip.top="'Ver Seguimiento'" />
            <Button icon="pi pi-send" class="p-button-rounded p-button-success" @click="derivarTramite(data)"
              v-tooltip.top="'Derivar'" />
            <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" @click="anularTramite(data)"
              v-tooltip.top="'Anular'" />
          </div>
        </template>
      </Column>

      <Column field="documento.numero_documento" header="N° Doc." sortable />

      <Column field="documento.fecha_emision" header="Fecha" sortable>
        <template #body="{ data }">
          {{ formatDate(data.documento.fecha_emision) }}
        </template>
      </Column>

      <Column field="documento.tipo_documento" header="Documento" sortable />
      <Column field="asunto" header="Asunto" sortable />
      <Column field="documento.numero_folios" header="Nro Folios" sortable />
      <Column field="oficinaDestino.name" header="Destino" sortable />

      <Column field="estado_destino" header="Estado Destino" sortable>
        <template #body="{ data }">
          {{ data.estado_destino || 'Pendiente' }}
        </template>
      </Column>

      <Column header="PDF" style="text-align: center;">
        <template #body="{ data }">
          <Button icon="pi pi-file-pdf" class="p-button-danger" text @click="verPdf(data.documento.pdf_url)"
            :disabled="!data.documento.pdf_url" />
        </template>
      </Column>

      <Column field="proveido" header="Proveído" sortable>
        <template #body="{ data }">
          {{ data.proveido || 'N/A' }}
        </template>
      </Column>

    </DataTable>
  </div>
</template>
