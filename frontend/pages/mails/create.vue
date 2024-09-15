<script setup>
import { useAuth } from "@/stores/auth.js";

definePageMeta({
  middleware: "auth"
})
const { $api } = useNuxtApp();

const store = useAuth();

const name = ref('');
const behavior = ref('normal');
const content = ref('');
const subject = ref('');

const loading = ref(false);
const success = ref(false);

const createMail = async () => {
  loading.value = true;
  try {
    const response = await $api.post('/api/mails', {
      name: name.value,
      subject: subject.value,
      behaviour: behavior.value,
      content: content.value,
    });
    if(response.data.name){
      success.value = true;
      name.value = "";
      subject.value = "";
      behavior.value = "";
    }
  } catch (e) {
    loading.value = false;
  }

  loading.value = false;
};

const logout = () => {
  store.logout();
  navigateTo('/')
}

const updateContent = (newContent) => {
  content.value = newContent;
};
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
    <div class="mt-4 w-3/4 pb-8">
      <h2 class="text-2xl font-bold">Créer un nouveau mail</h2>
      <div class="mt-4">
        <form @submit.prevent="createMail">
          <Input placeholder="Code vérification" label="Nom du mail" v-model="name"/>
          <label for="behaviour" class="flex flex-col my-2">
            Comportement
            <select id="behaviour" class="bg-transparent border border-red-600 py-2 rounded px-4" v-model="behavior">
              <option value="normal">Normal</option>
            </select>
          </label>
          <Input placeholder="Merci pour votre inscription..." label="Objet" color="normal" class="w-full my-2" v-model="subject"/>

          <div class="my-4">
            <p>Message</p>
            <Editor @updateContent="updateContent" />
          </div>

          <Button class="w-full" :loading="loading">Créer le mail</Button>
        </form>
        <div v-if="success" class="mt-4">
          <Success :state="success">Le mail a été créée</Success>
        </div>
      </div>
    </div>
  </div>
</template>
