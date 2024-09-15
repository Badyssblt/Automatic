
<template>
    <div class="mt-12 min-h-96">
      <h2 class="font-semibold text-3xl md:text-4xl text-center">Ne perdez plus de temps<br/> avec <span class="text-red-600">Automatic</span></h2>
      <p class="text-center mt-4">Découvrez la suite Automatic pour gagnez du temps<br/> sur vos tâches répétitives</p>
      <div class="flex justify-center mt-4" v-if="!isAuthenticated">
        <NavLink url="/register">Je m'inscris gratuitement</NavLink>
      </div>
      <div class="flex justify-center mt-4" v-else>
        <NavLink url="/mails">J'accède à mon compte</NavLink>
      </div>
    </div>
  <div>
      <h3 class="font-medium text-3xl text-center ">Outils disponibles gratuitement</h3>
      <div class="flex justify-center gap-24 mt-6">
        <div class="flex flex-col items-center w-fit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-24 text-red-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
          </svg>
          <h2 class="text-2xl ">Mails</h2>
          <p class="w-52 text-center text-sm text-gray-500">Envoyer facilement des emails avec vos propres données avec TailwindCSS grâce à notre API</p>
        </div>
        <div class="flex flex-col items-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-24 text-red-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
          </svg>

          <h2 class="text-2xl ">Données</h2>
          <p class="w-64 text-center text-sm text-gray-500">Sauvegarde de vos bases de données, exportations/importations dans différents formats</p>
        </div>
        <div class="flex flex-col items-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-24 text-red-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
          </svg>

          <h2 class="text-2xl ">Déploiement</h2>
          <p class="w-52 text-center text-sm text-gray-500">Déployer vos applications web en quelques minutes</p>
        </div>
      </div>
  </div>
</template>

<script setup>
import { useAuth } from "@/stores/auth.js";

const store = useAuth();

const isAuthenticated = ref(store.isAuthenticated)
</script>


