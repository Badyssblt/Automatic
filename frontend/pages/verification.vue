<template>
  <VerificationInput @codeComplete="handleCodeComplete" />
</template>

<script setup>
import Cookie from "js-cookie";

const { $api } = useNuxtApp();

const handleCodeComplete = async (code) => {
    try {
      const response = await $api.post('/api/verification', {
        code: parseInt(code),
        email: Cookie.get('temp_email')
      });
      if(response.status === 200){
        Cookie.remove('temp_email');
        navigateTo('/login');
      }
    }catch (e) {
      console.log(e)
    }
};
</script>
