<script setup>
import { ref, onMounted, watch } from 'vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import { seguimientoService } from '../services/seguimientoService.js';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';

const confirm = useConfirm();
const toast = useToast();

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
    // Limpia los campos cada vez que se abre el modal
    limpiarCampos(false); // Pasamos false para no mostrar el toast
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
  if (!tramiteData.value || !tramiteData.value.id) {
    console.error("ID de trámite no disponible");
    return;
  }
  const payload = {
    oficinas_id: destino.value.id,
    proveido: proveido.value,
  };
  try {
    await seguimientoService.derivarTramite(tramiteData.value.id, payload);
    toast.add({ severity: 'success', summary: 'Éxito', detail: 'Trámite derivado correctamente', life: 3000 });
    emit('success');
    visibleDialog.value = false;
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo derivar el trámite', life: 3000 });
    console.error("Error al derivar:", error);
  }
};

const confirmarDerivacion = () => {
  if (!destino.value || !proveido.value) {
    toast.add({ severity: 'warn', summary: 'Campos requeridos', detail: 'Debe seleccionar un destino y escribir un proveído.', life: 3000 });
    return;
  }
  confirm.require({
    message: `¿Está seguro de que desea derivar este trámite a la oficina de ${destino.value?.name || ''}?`,
    header: 'Confirmación de Derivación',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Sí, Derivar',
    rejectLabel: 'Cancelar',
    accept: () => {
      derivarTramite();
    },
    reject: () => {
      toast.add({ severity: 'info', summary: 'Cancelado', detail: 'La derivación ha sido cancelada.', life: 3000 });
    }
  });
};

</script>

<template>
  <Dialog v-model:visible="visibleDialog" header="Derivar Documento" :modal="true" style="width: 50vw;">
    <div class="p-fluid">
      <div class="field">
        <label>Documento a derivar: <strong>{{ tramiteData?.cu }}</strong></label>
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
