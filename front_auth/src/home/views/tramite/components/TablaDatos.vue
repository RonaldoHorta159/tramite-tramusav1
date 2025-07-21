<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

// Define las propiedades que el componente espera recibir
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

// --- CORRECCIÓN 1: Declarar 'filters' como una referencia reactiva ---
const filters = ref();

// Función para inicializar o reiniciar los filtros
const initFilters = () => {
  filters.value = {
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  };
};

// Llama a la función para establecer el estado inicial del filtro
initFilters();

// Función para limpiar el filtro (ahora simplemente reinicia)
const clearFilter = () => {
  initFilters();
};

// Función para dar formato a la fecha
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
    // Aquí podrías usar tu toast para notificar que no hay PDF
    console.warn('Este trámite no tiene un PDF adjunto.');
  }
};
</script>

<template>
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
    <Column field="estado_destino" header="Estado Destino" sortable style="min-width: 8rem"></Column>

    <Column header="PDF" style="min-width: 5rem; text-align: center;">
      <template #body="{ data }">
        <Button icon="pi pi-file-pdf" class="p-button-danger" text @click="verPdf(data.pdf_url)" />
      </template>
    </Column>

  </DataTable>
</template>
