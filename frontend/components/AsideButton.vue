<script setup>
  defineProps({
    url: {
      type: String,
      default: '/'
    }
  })
</script>

<template>
    <NuxtLink :to="url" class="flex gap-4 font-semibold"><slot/></NuxtLink>
</template>

