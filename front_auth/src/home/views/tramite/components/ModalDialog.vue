<script setup lang="ts">
import { ref } from "vue";
import Button from 'primevue/button'
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';

const visible = ref(false);
const selectedCountry = ref();
const src = ref(null);
const fileupload = ref();


</script>



<template>
  <Button label="Nuevo" @click="visible = true" />
  <Dialog v-model:visible="visible" modal header="Emitir documento" :style="{ width: '50rem' }">
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
      <Button type="button" label="Cancelar" severity="secondary" @click="visible = false" />
      <Button type="button" label="Enviar" @click="visible = false" />
    </div>
  </Dialog>


</template>
