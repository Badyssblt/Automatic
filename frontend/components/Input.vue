<template>
  <label class="flex flex-col">
    {{ label }}
    <input
        :type="type"
        v-model="model"
        :class="[
        'text-black px-4 py-2 rounded focus:outline-none',
        color === 'normal' ? 'bg-gray-200' : 'bg-transparent border border-red-600',
        disabled === true ? 'opacity-50' : ''
      ]"
        :placeholder="placeholder"
        :disabled="disabled"
    />
  </label>
</template>

<script setup>

const model = defineModel();


defineProps({
  label: String,
  type: {
    type: String,
    default: "text"
  },
  placeholder: {
    type: String,
    default: ""
  },
  color: {
    type: String,
    default: ""
  },
  disabled: {
    type: Boolean,
    default: false
  }
})
</script>

<style>

</style>