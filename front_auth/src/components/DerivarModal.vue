<script setup>
import { ref, watch, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { seguimientoService } from '@/services/seguimientoService';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';

const props = defineProps({
  visible: Boolean,
  tramite: Object, // Recibe el objeto completo del trámite a derivar
});
const emit = defineEmits(['close', 'tramite-derivado']);

const toast = useToast();
const isSubmitting = ref(false);
const oficinasList = ref([]);
const formData = ref({
  oficinas_destino: null,
  proveido: '',
});

// Carga la lista de oficinas cuando el componente se monta
onMounted(async () => {
  oficinasList.value = await seguimientoService.fetchOficinas();
});

const handleSubmit = async () => {
  if (!props.tramite || !formData.value.oficinas_destino || !formData.value.proveido) {
    toast.add({ severity: 'warn', summary: 'Campos incompletos', detail: 'Por favor, seleccione un destino y escriba el proveído.', life: 3000 });
    return;
  }
  isSubmitting.value = true;
  try {
    const data = {
      oficinas_destino: formData.value.oficinas_destino.id,
      proveido: formData.value.proveido,
    };
    const response = await seguimientoService.derivarTramite(props.tramite.id, data);
    toast.add({ severity: 'success', summary: 'Éxito', detail: response.message, life: 3000 });
    emit('tramite-derivado');
    closeModal();
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'Ocurrió un error al derivar.';
    toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 5000 });
  } finally {
    isSubmitting.value = false;
  }
};

const resetForm = () => {
  formData.value = { oficinas_destino: null, proveido: '' };
};

const closeModal = () => {
  resetForm();
  emit('close');
};

watch(() => props.visible, (isVisible) => {
  if (!isVisible) {
    resetForm();
  }
});
</script>

<template>
  <Dialog :visible="props.visible" @update:visible="closeModal" modal header="Derivar Trámite"
    :style="{ width: '40rem' }">
    <div class="p-fluid mt-4">
      <div v-if="props.tramite" class="mb-4">
        <p><strong>Derivando Trámite:</strong> {{ props.tramite.CU }}</p>
      </div>
      <div class="field mb-4">
        <label for="destino">Nuevo Destino</label>
        <Dropdown id="destino" v-model="formData.oficinas_destino" :options="oficinasList" optionLabel="name"
          placeholder="Seleccione una oficina" filter class="w-full" />
      </div>
      <div class="field">
        <label for="proveido">Pase a / Proveído (Instrucciones)</label>
        <Textarea id="proveido" v-model="formData.proveido" rows="5" style="resize: none;" />
      </div>
    </div>
    <template #footer>
      <Button label="Cancelar" icon="pi pi-times" @click="closeModal" severity="secondary" />
      <Button label="Derivar" icon="pi pi-send" @click="handleSubmit" :loading="isSubmitting" />
    </template>
  </Dialog>
</template>
