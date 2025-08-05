<script setup>
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


// 2. DEFINIMOS EL EMIT PARA COMUNICARNOS CON EL PADRE
const emit = defineEmits(['reload', 'abrir-recepcionar']);

const abrirModal = () => {
  emit('abrir-recepcionar');
};

const abrirSeguimiento = (data) => emit('ver-seguimiento', data);
const abrirObservar = (data) => emit('observar-tramite', data);

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
const rechazarTramite = (tramite) => emit('rechazar-tramite', tramite);
</script>

<template>
  <div>
    <DataTable :value="props.tramites" :loading="props.loading" paginator :rows="10" dataKey="id" showGridlines
      responsiveLayout="scroll" class="p-datatable-sm">
      <template #header>
        <div class="flex flex-wrap items-center justify-between gap-2">
          <span class="text-xl text-900 font-bold">Bandeja de Entrada</span>
          <div>
            <Button label="Recepcionar" icon="pi pi-inbox" class="mr-2" @click="abrirModal" />
            <Button icon="pi pi-refresh" rounded raised @click="$emit('reload')" />
          </div>
        </div>
      </template>

      <template #empty> No hay trámites pendientes por recibir. </template>
      <template #loading> Cargando trámites, por favor espere... </template>

      <Column field="CU" header="Código Único" sortable />
      <Column field="estado" header="Estado" sortable />

      <Column header="Opciones" :exportable="false" style="min-width:8rem">
        <template #body="slotProps">
          <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="abrirSeguimiento(slotProps.data)" />
          <Button icon="pi pi-pencil" outlined rounded severity="warning" class="mr-2"
            @click="abrirObservar(slotProps.data)" />
          <Button icon="pi pi-times-circle" class="p-button-rounded p-button-danger"
            @click="rechazarTramite(slotProps.data)" v-tooltip.top="'Rechazar'" />
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
