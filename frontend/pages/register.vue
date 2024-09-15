<script setup>
import Cookie from "js-cookie";
import { ref } from "vue";
import { useAuth } from "~/stores/auth.js";
import ProgressBar from "~/components/ProgressBar.vue";

const { $api } = useNuxtApp();
const store = useAuth();

const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const smtp = ref('');

const loading = ref(false);
const error = ref(false);

const step = ref('informations');
const currentWidth = ref('w-1/2');

const nextStep = (next, percentage) => {
  step.value = next;
  currentWidth.value = percentage;
}

const validatePasswords = () => {
  error.value = null
  if (password.value !== confirmPassword.value) {
    error.value = 'Les mots de passe ne correspondent pas';
    return false;
  }
  return true;
}

const handleNextStep = () => {
  if (validatePasswords()) {
    nextStep('mail', 'w-full');
  }
}

const login = async () => {
  loading.value = true;
  error.value = false;
  try {
    const response = await $api.post('/api/users', {
      email: email.value,
      password: password.value,
      smtp: smtp?.value
    });
    if (response.data.email) {
      Cookie.set('temp_email', response.data.email);
      navigateTo('/verification');
    }
  } catch (e) {
    error.value = 'Une erreur est survenue';
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="w-1/2 m-auto mt-4">
    <ProgressBar :width="currentWidth"/>
  </div>
  <div class="w-96 m-auto mt-12">
    <h2 class="font-bold text-2xl">Inscrivez vous</h2>
    <form @submit.prevent="login">
      <div v-if="step === 'informations'">
        <Input placeholder="johndoe@exemple.com" label="Email" v-model="email"/>
        <Input placeholder="*******" label="Mot de passe" type="password" class="mt-4" v-model="password"/>
        <Input placeholder="*******" label="Confirmez votre mot de passe" type="password" class="mt-4" v-model="confirmPassword"/>
        <Button class="w-full mt-4" type="button" @click="handleNextStep">Suivant</Button>
      </div>
      <div v-if="step === 'mail'">
        <Input placeholder="smtp://" label="Votre SMTP" class="mt-4" v-model="smtp"/>
      </div>
      <div v-if="step === 'mail'">
        <Button class="w-full mt-4" :loading="loading">S'inscrire</Button>
        <button class="flex items-center justify-center gap-4 px-6 py-2 rounded w-full border-red-600 text-red-600 border mt-4" @click="nextStep('informations', 'w-1/2')">Précédent</button>
      </div>
      <div class="mt-4" v-if="error">
        <Error :state="error" :time="999999999">{{ error }}</Error>
      </div>
      <NuxtLink class="flex justify-center mt-4 text-gray-400 transition-all hover:underline" to="/login">Ou se connecter</NuxtLink>
    </form>
  </div>
</template>


