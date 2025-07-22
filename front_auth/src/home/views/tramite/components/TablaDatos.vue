<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar'; // <-- Importa Toolbar
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

// El emit para comunicarse con el padre es correcto
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

const filters = ref();

const initFilters = () => {
  filters.value = {
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  };
};
initFilters();

const clearFilter = () => {
  initFilters();
};

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
    window.open(pdfUrl, '_blank');
  } else {
    console.warn('Este trámite no tiene un PDF adjunto.');
  }
};

// La función que emite el evento al padre es correcta
const openNew = () => {
  emit('open-new-modal');
};
</script>

<template>
  <div>
    <Toolbar class="mb-4">
      <template #start>
        <div class="my-2">
          <Button label="Nuevo Trámite" icon="pi pi-plus" class="p-button-success" @click="openNew" />
        </div>
      </template>
    </Toolbar>
    <DataTable v-model:filters="filters" :value="props.customers" paginator showGridlines :rows="10" dataKey="CU"
      :loading="props.loading" :globalFilterFields="['CU', 'asunto', 'estado', 'documento.fecha_emision']" :pt="{
        column: {
          headercell: { style: 'font-size: 12px; font-weight: bold;' },
          bodycell: { style: 'font-size: 15px' }
        }
      }">

      <template #header>
        <div class="flex justify-between items-center">
          <Button type="button" icon="pi pi-filter-slash" label="Limpiar" outlined @click="clearFilter()" />
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model="filters['global'].value" placeholder="Buscar en la tabla..." />
          </IconField>
        </div>
      </template>

      <template #empty> No se encontraron resultados. </template>
      <template #loading> Cargando datos, por favor espere... </template>

      <Column field="CU" header="Código Único" sortable style="min-width: 12rem"></Column>
      <Column field="asunto" header="Asunto" sortable style="min-width: 16rem"></Column>
      <Column field="estado" header="Estado" sortable style="min-width: 8rem"></Column>
      <Column field="documento.fecha_emision" header="Fecha de Emisión" sortable style="min-width: 10rem">
        <template #body="{ data }">
          {{ formatDate(data.documento.fecha_emision) }}
        </template>
      </Column>
      <Column field="documento.numero_folios" header="N° Folios" sortable style="min-width: 5rem"></Column>
      <Column field="oficinaDestino.name" header="Destino" sortable style="min-width: 8rem"></Column>
      <Column header="PDF" style="min-width: 5rem; text-align: center;">
        <template #body="{ data }">
          <Button icon="pi pi-file-pdf" class="p-button-danger" text @click="verPdf(data.documento.pdf_url)"
            :disabled="!data.documento.pdf_url" />
        </template>
      </Column>
    </DataTable>
  </div>
</template>
