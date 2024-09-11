<template>
  <div>
    <div class="flex gap-4">
      <input
          v-for="(code, index) in codes"
          :key="index"
          v-model="codes[index]"
          :ref="el => inputRefs[index] = el"
          maxlength="1"
          @input="moveToNext(index)"
          @keydown.backspace="moveToPrevious(index, $event)"
          @paste="handlePaste($event)"
          class="border w-12 h-12 flex justify-center text-center focus:outline-none"
      />
    </div>
  </div>
</template>

<script setup>

const emit = defineEmits(['codeComplete'])

const codes = ref(['', '', '', '', '', '']);
const inputRefs = ref([]);

const moveToNext = (index) => {
  if (codes.value[index] !== '' && index < codes.value.length - 1) {
    inputRefs.value[index + 1]?.focus();
  }
  checkComplete();
};

const moveToPrevious = (index, event) => {
  if (event.key === 'Backspace' && index > 0 && codes.value[index] === '') {
    inputRefs.value[index - 1]?.focus();
  }
};

const handlePaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');

  for (let i = 0; i < pastedData.length && i < codes.value.length; i++) {
    codes.value[i] = pastedData[i];
  }
  inputRefs.value[pastedData.length - 1]?.focus();
  checkComplete();
};

const checkComplete = () => {
  if (codes.value.every(code => code !== '')) {
    emit('codeComplete', codes.value.join(''));
  }
};
</script>
