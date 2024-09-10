<script setup>
import Cookie from "js-cookie";
import { jwtDecode } from "jwt-decode";
import {useAuth} from "~/stores/auth.js";

const { $api } = useNuxtApp();

  const store = useAuth();

  const email = ref('');
  const password = ref('');

  const loading = ref(false);
  const error = ref(false);

  const login = async () => {
    loading.value = true;
    error.value = false;
    try {
      const response = await $api.post('/api/login_check', {
        username: email.value,
        password: password.value
      });
      if(response.data.token){
        const decoded = jwtDecode(response.data.token)
        store.authenticate({
          email: decoded.username,
          roles: decoded.roles
        });
        store.token = response.data.token;
        navigateTo('/')
      }
    }catch (e) {
      error.value = true;
      loading.value = false
    }

    loading.value = false;
  }
</script>

<template>
    <div class="w-96 m-auto mt-12">

      <h2 class="font-bold text-2xl">Connectez vous</h2>
      <form @submit.prevent="login">
        <Input placeholder="johndoe@exemple.com" label="Email" v-model="email"/>
        <Input placeholder="*******" label="Mot de passe" type="password" class="mt-4" v-model="password"/>
        <Button class="w-full mt-4" :loading="loading">Se connecter</Button>
        <div class="mt-4" v-if="error">
          <Error :state="error" :time="999999999">Email ou mot de passe incorrect</Error>
        </div>
      </form>
    </div>
</template>

<style scoped>

</style>