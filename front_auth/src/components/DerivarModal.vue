<script setup>
import { ref, onMounted, watch } from 'vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import { seguimientoService } from '../services/seguimientoService.js';
import { useAuthStore } from '../home/store/authStore'; // <-- AÑADIDO
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import InputText from 'primevue/inputtext'; // <-- AÑADIDO

const confirm = useConfirm();
const toast = useToast();
const authStore = useAuthStore(); // <-- AÑADIDO

const props = defineProps({
  visible: Boolean,
  tramite: Object
});

const emit = defineEmits(['update:visible', 'success']);

const visibleDialog = ref(props.visible);
const oficinas = ref([]);
const destino = ref(null);
const proveido = ref('');
const tramiteData = ref(props.tramite);

watch(() => props.visible, (newVal) => {
  visibleDialog.value = newVal;
  if (newVal) {
    limpiarCampos(false);
  }
});

watch(() => props.tramite, (newVal) => {
  tramiteData.value = newVal;
});

watch(visibleDialog, (newVal) => {
  if (!newVal) {
    emit('update:visible', false);
  }
});

onMounted(async () => {
  try {
    const response = await seguimientoService.fetchOficinas();
    oficinas.value = response;
  } catch (error) {
    console.error("Error al cargar oficinas:", error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar las oficinas.', life: 3000 });
  }
});

const limpiarCampos = (showToast = true) => {
  destino.value = null;
  proveido.value = '';
  if (showToast) {
    toast.add({ severity: 'info', summary: 'Campos Limpiados', detail: 'Puede ingresar nuevos datos.', life: 2000 });
  }
};

const derivarTramite = async () => {
  // ... (sin cambios aquí)
};

const confirmarDerivacion = () => {
  // ... (sin cambios aquí)
};

</script>

<template>
  <Dialog v-model:visible="visibleDialog" header="Derivar Documento" :modal="true" style="width: 50vw;">
    <div class="p-fluid">
      <div class="field mb-4">
        <label>Documento a derivar: <strong>{{ tramiteData?.cu }}</strong></label>
      </div>

      <div class="grid formgrid">
        <div class="col-12 md:col-6 field">
          <label for="accion">Acción</label>
          <InputText id="accion" type="text" value="DERIVAR" disabled />
        </div>
        <div class="col-12 md:col-6 field">
          <label for="origen">Oficina Origen</label>
          <InputText id="origen" type="text" :model-value="authStore.user?.oficina?.name" disabled />
        </div>
      </div>
      <div class="field">
        <label for="destino">Nuevo Destino</label>
        <Dropdown id="destino" v-model="destino" :options="oficinas" filter optionLabel="name"
          placeholder="Seleccione una oficina" class="w-full" />
      </div>
      <div class="field">
        <label for="proveido">Pase a / Proveído</label>
        <Textarea id="proveido" v-model="proveido" rows="3" class="w-full" />
      </div>
    </div>
    <template #footer>
      <Button label="Cerrar" icon="pi pi-times" @click="visibleDialog = false" class="p-button-text" />
      <Button label="Limpiar" icon="pi pi-eraser" @click="limpiarCampos()" severity="secondary" outlined />
      <Button label="Derivar" icon="pi pi-send" @click="confirmarDerivacion" :disabled="!destino || !proveido" />
    </template>
  </Dialog>
</template>
