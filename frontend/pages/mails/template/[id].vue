<script setup>

const route = useRoute();
const { $api } = useNuxtApp();
const id = ref(route.params.id);

definePageMeta({
  middleware: "auth"
})

const name = ref('');
const behavior = ref();
const subject = ref('');
const content = ref('');
const is_deploy = ref(false);

const mail = ref();
const loading = ref(false);
const success = ref(false);

const fetchMail = async () => {
  try {
    const response = await $api.get(`/api/mails_template/${id.value}`);
    if(response.data){
      mail.value = response.data;
      name.value = mail.value.name;
      behavior.value = mail.value.behaviour;
      subject.value = mail.value.subject;
      is_deploy.value = mail.value.deploy;
      content.value = mail.value.content;
    }
  }catch (e) {
    if(e.status === 401){
      navigateTo('/mails')
    }
  }
}

const toggleDeploy = async () => {
  try {
    const response = await $api.patch(`/api/mails/${id.value}`, {
      deploy: is_deploy.value
    });
  }catch (e){

  }
}

const updateContent = (newContent) => {
  content.value = newContent
}

const deleteMail = async () => {
  try {
    const response = await $api.delete(`/api/mail/${id.value}`);
    if(response.status === 204){
      navigateTo('/mails');
    }
  }catch (e) {

  }
}

const createMail = async () => {
  loading.value = true;
  try {
    const response = await $api.post(`/api/mails`, {
      name: name.value,
      behaviour: behavior.value,
      content: content.value,
      subject: subject.value
    });
    if(response.data){
      success.value = true;
    }
  }catch (e) {
  }
  loading.value = false;
}

onMounted(async () => {
  await fetchMail();
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
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
      </svg>
        Créer un nouveau mail</AsideButton>
    </Aside>
    <div class="mt-4 w-full">
      <div class="flex items-center gap-4">
        <h2 class="text-2xl font-bold">Déployer</h2>
        <button type="button" @click="deleteMail">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
          </svg>
        </button>
      </div>
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
          <Editor @updateContent="updateContent" :defaultContent="content"/>
        </div>

        <Button class="w-full" :loading="loading">Créer le mail avec ce template</Button>
      </form>
      <div v-if="success" class="mt-4">
        <Success :state="success">Le mail a été créée</Success>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>