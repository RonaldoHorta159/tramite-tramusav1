<script setup>
import { defineProps, defineEmits } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const props = defineProps({
  tramites: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['recibir-tramite', 'observar-tramite', 'rechazar-tramite']);

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

// Funciones que emiten eventos al componente padre
const recibirTramite = (tramite) => emit('recibir-tramite', tramite);
const observarTramite = (tramite) => emit('observar-tramite', tramite);
const rechazarTramite = (tramite) => emit('rechazar-tramite', tramite);
</script>

<template>
  <div>
    <DataTable :value="props.tramites" :loading="props.loading" paginator :rows="10" dataKey="id" showGridlines
      responsiveLayout="scroll" class="p-datatable-sm">

      <template #empty> No hay trámites pendientes por recibir. </template>
      <template #loading> Cargando trámites, por favor espere... </template>

      <Column field="CU" header="Código Único" sortable />

      <Column field="estado" header="Estado" sortable />

      <Column header="Opciones" style="min-width: 10rem; text-align: center;">
        <template #body="{ data }">
          <div class="flex gap-2 justify-center">
            <Button icon="pi pi-inbox" class="p-button-rounded p-button-success" @click="recibirTramite(data)"
              v-tooltip.top="'Recibir Trámite'" />
            <Button icon="pi pi-comments" class="p-button-rounded p-button-warning" @click="observarTramite(data)"
              v-tooltip.top="'Observar'" />
            <Button icon="pi pi-times-circle" class="p-button-rounded p-button-danger" @click="rechazarTramite(data)"
              v-tooltip.top="'Rechazar'" />
          </div>
        </template>
      </Column>

      <Column field="numero_libro" header="N° Libro" sortable>
        <template #body="{ data }">
          {{ data.numero_libro || 'N/A' }}
        </template>
      </Column>

      <Column field="documento.fecha_emision" header="Fecha" sortable>
        <template #body="{ data }">
          {{ formatDate(data.documento.fecha_emision) }}
        </template>
      </Column>

      <Column field="documento.tipo_documento" header="Documento" sortable />
      <Column field="asunto" header="Asunto" sortable />
      <Column field="documento.numero_folios" header="Nro Folios" sortable />
      <Column field="oficinaOrigen.name" header="Origen" sortable />

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
