<script setup>
  defineProps({
    url: {
      type: String,
      default: "/"
    }
  })
</script>

<template>
  <NuxtLink :to="url" class="bg-red-600 text-white px-6 py-2 rounded"><slot/></NuxtLink>
</template>

