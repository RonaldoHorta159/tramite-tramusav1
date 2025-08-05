<script setup>
// --- PASO 1: Importaciones corregidas ---
import { ref } from 'vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button'
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';


const confirm = useConfirm()
const toast = useToast()

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(['update:visible', 'tramite-creado']);

const closeModal = () => {
  emit('update:visible', false);
};

// --- PASO 2: Función de envío ---
const enviarTramite = () => {
  toast.add({ severity: 'success', summary: 'Confirmado', detail: 'El trámite ha sido enviado.', life: 3000 });
  closeModal(); // Cierra el modal principal después de enviar
};

// --- PASO 3: Función de confirmación ---
const confirmarEnvio = () => {
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
        <Select v-model="selectedCountry" filter optionLabel="name" class="w-full  p-3 " :style="{ width: '15rem' }">
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
        <InputText id="on_label" />
        <label for="on_label">Numero de Documento</label>
      </FloatLabel>
      <FloatLabel variant="on" class="ml-8">
        <InputText id="on_label" />
        <label for="on_label">Numero de Folios</label>
      </FloatLabel>
    </div>
    <div class="flex items-center gap-4 mb-8">
      <FloatLabel variant="on">
        <Select v-model="selectedCountry" filter optionLabel="name" class="w-full mt-5 p-3 "
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
        <Textarea id="over_label" rows="5" cols="96" style="resize: none" />
        <label for="on_label">Asunto</label>
      </FloatLabel>
    </div>
    <label for="File">Elegir Documento</label>
    <div class="flex items-center gap-4 mb-8 mt-2">
      <FileUpload ref="fileupload" mode="basic" name="demo[]" :maxFileSize="1000000"
        chooseLabel="Seleccionar archivo" />
    </div>
    <div class="flex justify-end gap-2 mt-4">
      <Button type="button" label="Cancelar" severity="secondary" @click="closeModal" />
      <Button type="button" label="Enviar" @click="confirmarEnvio" />
    </div>
  </Dialog>
</template>
