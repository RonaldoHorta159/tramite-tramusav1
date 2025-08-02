<script setup>
import { ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { seguimientoService } from '@/services/seguimientoService';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';

const props = defineProps({
  visible: Boolean,
  tramite: Object,
});
const emit = defineEmits(['close', 'tramite-observado']);

const toast = useToast();
const isSubmitting = ref(false);
const proveido = ref('');

const handleSubmit = async () => {
  if (!proveido.value) {
    toast.add({ severity: 'warn', summary: 'Campo vacío', detail: 'Por favor, escriba la observación.', life: 3000 });
    return;
  }
  isSubmitting.value = true;
  try {
    const response = await seguimientoService.observarTramite(props.tramite.id, { proveido: proveido.value });
    toast.add({ severity: 'success', summary: 'Éxito', detail: response.message, life: 3000 });
    emit('tramite-observado');
    closeModal();
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'Ocurrió un error al guardar la observación.';
    toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 5000 });
  } finally {
    isSubmitting.value = false;
  }
};

const closeModal = () => {
  proveido.value = ''; // Limpiar el textarea
  emit('close');
};

watch(() => props.tramite, (newTramite) => {
  if (newTramite) {
    // Si el trámite ya tiene un proveído, lo mostramos para editarlo
    proveido.value = newTramite.proveido || '';
  }
});
</script>

<template>
  <Dialog :visible="props.visible" @update:visible="closeModal" modal header="Añadir Observación"
    :style="{ width: '40rem' }">
    <div class="p-fluid mt-4">
      <div v-if="props.tramite" class="mb-4">
        <p><strong>Observando Trámite:</strong> {{ props.tramite.CU }}</p>
      </div>
      <div class="field">
        <label for="proveido">Observación / Proveído</label>
        <Textarea id="proveido" v-model="proveido" rows="5" style="resize: none;" autoFocus />
      </div>
    </div>
    <template #footer>
      <Button label="Cancelar" icon="pi pi-times" @click="closeModal" severity="secondary" />
      <Button label="Guardar Observación" icon="pi pi-save" @click="handleSubmit" :loading="isSubmitting" />
    </template>
  </Dialog>
</template>
