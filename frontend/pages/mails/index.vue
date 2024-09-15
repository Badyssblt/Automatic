<script setup>
import { useAuth } from "@/stores/auth.js";

definePageMeta({
  middleware: "auth"
})

  const $config  = useRuntimeConfig();
  const { $api } = useNuxtApp();
  const store = useAuth();

  const isCopy = ref(false);

  const mails = ref([]);
  const apiKey = ref('');

const fetchMails = async () => {
  try {
    const response = await $api.get('/api/mails')
    mails.value = response.data;
  }catch (e) {
    console.log(e)
  }
}

const getApiKey = async () => {
  try {
      const response = await $api.get('/api/apiKey');
      apiKey.value = response.data.keys[0];
  }catch (e){
    console.log(e)
  }
}

const logout = () => {
  store.logout();
  navigateTo('/')
}

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
      .then(() => {
        isCopy.value = true;
      })
      .catch(err => {
      });
}

const truncateText = (text, maxLength) => {
  if(text.length <= maxLength){
    return text;
  }
  return text.slice(0, maxLength) + '...';
}

onMounted(async () => {
  await fetchMails();
  await getApiKey();
})



</script>

<template>
  <div class="mx-auto max-w-screen-xl flex gap-6">
    <Aside>
      <AsideButton url="/mails"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
      </svg>
        Tous mes mails</AsideButton>
      <AsideButton url="/mails/create"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
      </svg>

        Créer un nouveau mail</AsideButton>
<!--      <AsideButton url="/mails/deploy"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">-->
<!--        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />-->
<!--      </svg>-->
<!--        Déployer un mail</AsideButton>-->

      <button @click="logout">
        Se déconnecter
      </button>
    </Aside>
    <div class="mt-4">
      <h2 class="text-2xl font-bold">Mes mails</h2>
      <div class="flex items-center gap-2 border p-2 rounded">
        <span>Ma clé API: {{ truncateText(apiKey, 10)  }}</span>
        <button class="border p-1 rounded" @click="copyToClipboard(apiKey)">
          <svg v-if="!isCopy" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
          </svg>
          <svg v-if="isCopy" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
          </svg>
        </button>
      </div>
      <div class="flex flex-wrap mt-4 gap-4 w-full" v-if="mails && mails.length > 0">
        <MailCard v-for="mail in mails" :mail="mail" :key="mail.id"/>
      </div>
      <div v-else class="flex gap-4">
        <p>Vous n'avez aucun mail...</p>
        <NuxtLink to="/mails/create" class="text-red-600 font-semibold">Créez-en un</NuxtLink>
      </div>
    </div>
  </div>
</template>

