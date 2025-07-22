<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { FilterMatchMode } from '@primevue/core/api'; // Ya no se necesita FilterOperator
import Button from 'primevue/button'
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';


const customers = ref();
const filters = ref();
const loading = ref(true);



// 1. FUNCIÓN DE FILTROS SIMPLIFICADA
const initFilters = () => {
  filters.value = {
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  };
};

initFilters(); // Llama a la nueva función simplificada

const formatDate = (value: any) => {
  return new Date(value).toLocaleDateString('es-ES', { // Cambiado a 'es-ES' como ejemplo
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};
const formatCurrency = (value: any) => {
  return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};

// Esta función ahora solo reinicia el filtro global
const clearFilter = () => {
  initFilters();
};

const getCustomers = (data: any) => {
  return [...(data || [])].map((d) => {
    d.date = new Date(d.date);
    return d;
  });
};

</script>

<template>
  <DataTable v-model:filters="filters" :value="customers" paginator showGridlines :rows="10" dataKey="id"
    :loading="loading" :globalFilterFields="['name', 'country.name', 'representative.name', 'status']" :pt="{
      column: {
        headercell: { style: 'font-size: 10px' },
        bodycell: { style: 'font-size: 15px' }
      }
    }">
    <template #header>
      <div class="flex justify-between">
        <Button type="button" icon="pi pi-filter-slash" label="Limpiar" outlined @click="clearFilter()" />
        <IconField>
          <InputIcon>
            <i class="pi pi-search" />
          </InputIcon>
          <InputText v-model="filters['global'].value" placeholder="Buscar en la tabla..." />
        </IconField>
      </div>
    </template>
    <template #empty> No se encontro lo que buscaba.</template>
    <template #loading> Cargando datos. Por favor espere. </template>

    <Column field="name" header="Codigo Unico" style="min-width: 8rem">
      <template #body="{ data }">
        {{ data.name }}
      </template>
    </Column>

    <Column field="country.name" header="Opciones" style="min-width: 8rem">
      <template #body="{ data }">
        <div class="flex items-center gap-2">
          <img alt="flag" src="https://primefaces.org/cdn/primevue/images/flag/flag_placeholder.png"
            :class="`flag flag-${data.country.code}`" style="width: 24px" />
          <span>{{ data.country.name }}</span>
        </div>
      </template>
    </Column>

    <Column field="representative.name" header="N° Documento" style="min-width: 3rem">
      <template #body="{ data }">
        <div class="flex items-center gap-2">
          <img :alt="data.representative.name"
            :src="`https://primefaces.org/cdn/primevue/images/avatar/${data.representative.image}`"
            style="width: 32px" />
          <span>{{ data.representative.name }}</span>
        </div>
      </template>
    </Column>

    <Column field="date" header="Fecha" style="min-width: 5rem">
      <template #body="{ data }">
        {{ formatDate(data.date) }}
      </template>
    </Column>

    <Column field="balance" header="Documento" dataType="numeric" style="min-width:7rem">
      <template #body="{ data }">
        {{ formatCurrency(data.balance) }}
      </template>
    </Column>

    <Column field="status" header="Asunto" style="min-width: 6rem">
      <template #body="{ data }">
        {{ data.status }}
      </template>
    </Column>

    <Column field="activity" header="Nro Folios" style="min-width: 3rem">
      <template #body="{ data }">
        {{ data.activity }}
      </template>
    </Column>

    <Column field="verified" header="Destino" dataType="boolean" bodyClass="text-center" style="min-width: 5rem">
      <template #body="{ data }">
        <i class="pi"
          :class="{ 'pi-check-circle text-green-500': data.verified, 'pi-times-circle text-red-400': !data.verified }"></i>
      </template>
    </Column>

    <Column field="EstadoDestino" header="Estado Destino" style="min-width: 3rem"></Column>
    <Column field="pdf" header="PDF"></Column>

  </DataTable>
</template>
