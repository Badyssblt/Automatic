<script setup>

const props = defineProps({
  state: {
    type: Boolean,
    default: false
  },
  time: {
    type: Number,
    default: 3000
  }
});

const state = ref(props.state);
onMounted(() => {
  setTimeout(() => {
    state.value = false;
  }, props.time)
})

</script>
<template>
  <div role="alert" class="rounded border-s-4 border-red-500 bg-red-50 p-4" v-if="state">
    <strong class="block font-medium text-red-800"> Une erreur est survenue </strong>

    <p class="mt-2 text-sm text-red-700">
      <slot/>
    </p>
  </div>
</template>

<style scoped>

</style>