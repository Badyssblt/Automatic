<script setup>
import Cookie from "js-cookie";

  const { $api } = useNuxtApp();

  const email = ref('');
  const password = ref('');

  const login = async () => {
    try {
      const response = await $api.post('/api/login_check', {
        username: email.value,
        password: password.value
      });
      if(response.data.token){
        Cookie.set('token', response.data.token);
        navigateTo('/')
      }
    }catch (e) {

    }
  }
</script>

<template>
    <div class="w-96 m-auto mt-12">

      <h2 class="font-bold text-2xl">Connectez vous</h2>
      <form @submit.prevent="login">
        <Input placeholder="johndoe@exemple.com" label="Email" v-model="email"/>
        <Input placeholder="*******" label="Mot de passe" type="password" class="mt-4" v-model="password"/>
        <Button class="w-full mt-4">Se connecter</Button>
      </form>
    </div>
</template>

<style scoped>

</style>