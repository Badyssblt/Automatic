<script setup>

  const route = useRoute();
  const { $api } = useNuxtApp();
  const id = ref(route.params.id);


  const name = ref('');
  const behavior = ref();
  const subject = ref('');
  const content = ref('');
  const is_deploy = ref(false);

  const mail = ref();
  const loading = ref(false);

  const fetchMail = async () => {
    try {
      const response = await $api.get(`/api/mail/${id.value}`);
      if(response.data){
        console.log(response.data)
        mail.value = response.data;
        name.value = mail.value.name;
        behavior.value = mail.value.behaviour;
        subject.value = mail.value.subject;
        is_deploy.value = mail.value.deploy;
      }
    }catch (e) {
      console.log(e)
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
      <AsideButton url="/mails/deploy"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
      </svg>
        Déployer un mail</AsideButton>
    </Aside>
    <div class="mt-4 w-full">
      <h2 class="text-2xl font-bold">Déployer</h2>
      <form @submit.prevent="createMail">
        <label class="inline-flex items-center cursor-pointer my-4">
          <input type="checkbox" value="" class="sr-only peer" v-model="is_deploy" @change="toggleDeploy">
          <div class="relative w-11 h-6 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300  rounded-full peer bg-slate-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all  peer-checked:bg-red-600"></div>
          <span class="ms-3 text-sm font-medium text-gray-900 ">{{ is_deploy === true ? 'Activé' : 'Désactivé' }}</span>
        </label>
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

        <Button class="w-full" :loading="loading">Modifier le mail</Button>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>