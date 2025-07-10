<script setup lang="ts">
import { ref } from "vue";
import Button from 'primevue/button'
import Dialog from 'primevue/dialog';
import TablaDatosRecibir from "./TablaDatosRecibir.vue";

const visible = ref(false);
const selectedCountry = ref();
const src = ref(null);
const fileupload = ref();


</script>



<template>
  <Button label="Recibir" @click="visible = true" />
  <Dialog v-model:visible="visible" modal header="Emitir documento" :style="{ width: '50rem' }">
    <TablaDatosRecibir />
  </Dialog>
</template>
