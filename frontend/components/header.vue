<script setup>
import { ref } from 'vue';
import { useAuth } from "~/stores/auth.js";
import MenuOnHover from "~/components/MenuOnHover.vue";

const store = useAuth();
const isAuthenticated = ref(store.isAuthenticated);

const menuVisible = ref(false);

watch(
    () => store.isAuthenticated,
    (newVal) => {
      isAuthenticated.value = newVal;
    }
);
</script>

<template>
  <header class="border border-b py-4">
    <menu class="flex justify-between items-center mx-auto max-w-screen-xl">
      <div class="flex gap-4 items-end">
        <NuxtLink to="/"><h1 class="font-bold text-xl">Automatic</h1></NuxtLink>

        <div class="relative"
             @mouseenter="menuVisible = true"
             @mouseleave="menuVisible = false">
          <button>Outils</button>

          <MenuOnHover class="absolute top-full left-0" :visible="menuVisible" />
        </div>
      </div>

      <div class="flex gap-4 items-center" v-if="!isAuthenticated">
        <NuxtLink to="/login">Se connecter</NuxtLink>
        <NavLink url="/register">S'inscrire</NavLink>
      </div>
      <div class="flex gap-4 items-center" v-else>
        <NavLink url="/mails">Mon compte</NavLink>
      </div>
    </menu>
  </header>
</template>
