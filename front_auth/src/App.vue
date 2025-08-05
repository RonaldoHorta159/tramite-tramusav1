<template>
  <RouterView />
  <Toast />
  <ConfirmDialog /> <!-- <-- AÑADE ESTA LÍNEA -->
</template>


<script setup>
import ConfirmDialog from 'primevue/confirmdialog'; // <-- AÑADE ESTA LÍNEA
import Toast from 'primevue/toast'; // <-- AÑADE ESTA LÍNEA
</script>
