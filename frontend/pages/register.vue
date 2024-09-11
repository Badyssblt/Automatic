<script setup>
import Cookie from "js-cookie";
import { jwtDecode } from "jwt-decode";
import {useAuth} from "~/stores/auth.js";

const { $api } = useNuxtApp();

const store = useAuth();

const email = ref('');
const password = ref('');
const confirmPassword = ref('');

const loading = ref(false);
const error = ref(false);

const login = async () => {
  loading.value = true;
  error.value = false;
  if(password.value !== confirmPassword.value){
    error.value = ref('Les mots de passes ne correspondent pas');
    loading.value = false;
    return;
  }
  try {
    const response = await $api.post('/api/users', {
      email: email.value,
      password: password.value
    });
    if(response.data.email){
      Cookie.set('temp_email', response.data.email)
      navigateTo('/verification');
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

    <h2 class="font-bold text-2xl">Inscrivez vous</h2>
    <form @submit.prevent="login">
      <Input placeholder="johndoe@exemple.com" label="Email" v-model="email"/>
      <Input placeholder="*******" label="Mot de passe" type="password" class="mt-4" v-model="password"/>
      <Input placeholder="*******" label="Confirmez votre mot de passe" type="password" class="mt-4" v-model="confirmPassword"/>
      <Button class="w-full mt-4" :loading="loading">S'inscrire</Button>
      <div class="mt-4" v-if="error">
        <Error :state="error" :time="999999999">{{ error }}</Error>
      </div>
    </form>
  </div>
</template>

<style scoped>

</style>