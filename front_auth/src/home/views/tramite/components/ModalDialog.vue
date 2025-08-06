<script setup>
import { ref, onMounted } from 'vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import { seguimientoService } from '../../../../services/seguimientoService';
import Button from 'primevue/button'
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';
import documentoService from '@/services/documento.service.js';

const confirm = useConfirm()
const toast = useToast()

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(['update:visible', 'tramiteCreado']);

// --- 1. VARIABLES PARA MANEJAR EL FORMULARIO ---
const tipoDocumento = ref(null);
const numDocumento = ref('');
const numFolios = ref('');
const destino = ref(null);
const asunto = ref('');
const archivoPdf = ref(null);
const isLoading = ref(false);

// --- 2. LISTAS PARA LOS SELECTS ---
const oficinas = ref([]);
const tiposDeDocumento = ref([
  { name: 'Oficio' },
  { name: 'Memorando' },
  { name: 'Informe' },
  { name: 'Carta' },
  { name: 'Solicitud' },
]);

// --- 3. FUNCIÓN PARA CARGAR DATOS CUANDO EL COMPONENTE SE MONTA ---
onMounted(async () => {
  try {
    const response = await seguimientoService.fetchOficinas();
    oficinas.value = response;
  } catch (error) {
    console.error("Error al cargar oficinas:", error);
    toast.add({ severity: 'error', summary: 'Error de Carga', detail: 'No se pudieron cargar las oficinas.', life: 3000 });
  }
});


const closeModal = () => {
  emit('update:visible', false);
};

const handleFileUpload = (event) => {
  archivoPdf.value = event.files[0];
};

const limpiarFormulario = () => {
  tipoDocumento.value = null;
  numDocumento.value = '';
  numFolios.value = '';
  destino.value = null;
  asunto.value = '';
  archivoPdf.value = null;
}

const enviarTramite = async () => {
  isLoading.value = true;
  const formData = new FormData();

  // --- CORRECCIONES EN LOS NOMBRES DE LOS CAMPOS ---
  formData.append('tipo_documento', tipoDocumento.value?.name || '');
  formData.append('numero_documento', numDocumento.value || ''); // Se cambió 'num_documento' a 'numero_documento'
  formData.append('numero_folios', numFolios.value || '');     // Se cambió 'num_folios' a 'numero_folios'
  formData.append('oficinas_destino', destino.value?.id || ''); // Se cambió 'destino_id' a 'oficinas_destino'
  formData.append('asunto', asunto.value || '');
  if (archivoPdf.value) {
    formData.append('pdf', archivoPdf.value);
  }
  // --- FIN DE LAS CORRECCIONES ---

  try {
    await documentoService.createDocumento(formData);
    toast.add({ severity: 'success', summary: 'Éxito', detail: 'Trámite registrado correctamente.', life: 3000 });
    limpiarFormulario();
    emit('tramiteCreado');
    closeModal();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors;
      let errorMessages = '';
      for (const key in errors) {
        errorMessages += `${errors[key].join(', ')}\n`;
      }
      toast.add({
        severity: 'error',
        summary: 'Error de Validación',
        detail: errorMessages || 'Por favor, revise los datos del formulario.',
        life: 6000
      });
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error Inesperado',
        detail: 'No se pudo registrar el trámite. Intente de nuevo.',
        life: 3000
      });
    }
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const confirmarEnvio = () => {
  // ... (la lógica de confirmación que ya tenías)
  confirm.require({
    message: '¿Está seguro de que desea enviar este trámite?',
    header: 'Confirmación',
    icon: 'pi pi-info-circle',
    acceptLabel: 'Sí, enviar',
    rejectLabel: 'Cancelar',
    accept: () => {
      enviarTramite();
    },
    reject: () => {
      toast.add({ severity: 'info', summary: 'Cancelado', detail: 'Envío cancelado', life: 3000 });
    }
  });
};

</script>
<template>
  <Dialog :visible="props.visible" @update:visible="closeModal" modal header="Emitir documento"
    :style="{ width: '50rem' }">
    <div class="flex items-center gap-4 mb-4 mt-2">
      <FloatLabel variant="on">
        <Select v-model="tipoDocumento" :options="tiposDeDocumento" filter optionLabel="name" class="w-full  p-3 "
          :style="{ width: '15rem' }">
          <template #value="slotProps">
            <label for="on_label">Tipo de Documento</label>
            <div v-if="slotProps.value" class="flex items-center">
              <div>{{ slotProps.value.name }}</div>
            </div>
            <span v-else>
              {{ slotProps.placeholder }}
            </span>
          </template>
          <template #option="slotProps">
            <div class="flex items-center">
              <div>{{ slotProps.option.name }}</div>
            </div>
          </template>
        </Select>
      </FloatLabel>
      <FloatLabel variant="on" class="ml-7">
        <InputText id="on_label" v-model="numDocumento" />
        <label for="on_label">Numero de Documento</label>
      </FloatLabel>
      <FloatLabel variant="on" class="ml-8">
        <InputText id="on_label" v-model="numFolios" type="number" />
        <label for="on_label">Numero de Folios</label>
      </FloatLabel>
    </div>
    <div class="flex items-center gap-4 mb-8">
      <FloatLabel variant="on">
        <Select v-model="destino" :options="oficinas" filter optionLabel="name" class="w-full mt-5 p-3 "
          :style="{ width: '47rem' }">
          <template #value="slotProps">
            <label for="on_label">Destino</label>
            <div v-if="slotProps.value" class="flex items-center">
              <div>{{ slotProps.value.name }}</div>
            </div>
            <span v-else>
              {{ slotProps.placeholder }}
            </span>
          </template>
          <template #option="slotProps">
            <div class="flex items-center">
              <div>{{ slotProps.option.name }}</div>
            </div>
          </template>
        </Select>
      </FloatLabel>
    </div>
    <div class="flex items-center gap-4 mb-4 mt-2">
      <FloatLabel variant="on">
        <Textarea id="over_label" v-model="asunto" rows="5" cols="96" style="resize: none" />
        <label for="on_label">Asunto</label>
      </FloatLabel>
    </div>
    <label for="File">Elegir Documento</label>
    <div class="flex items-center gap-4 mb-8 mt-2">
      <FileUpload @select="handleFileUpload" ref="fileupload" mode="basic" name="demo[]" :maxFileSize="1000000"
        chooseLabel="Seleccionar archivo" />
    </div>
    <div class="flex justify-end gap-2 mt-4">
      <Button type="button" label="Cancelar" severity="secondary" @click="closeModal" />
      <Button type="button" label="Enviar" @click="confirmarEnvio" :loading="isLoading" />
    </div>
  </Dialog>
</template>
