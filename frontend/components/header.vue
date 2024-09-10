<script setup>
  import { useAuth } from "~/stores/auth.js";

  const store = useAuth();

  const isAuthenticated = store.isAuthenticated;

</script>

<template>
    <header class="border border-b py-4">
      <menu class="flex justify-between items-center mx-auto max-w-screen-xl">

        <div class="flex gap-4 items-end">
          <h1 class="font-bold text-xl">Automatic</h1>
          <button>Outils</button>
        </div>
        <div class="flex gap-4 items-center" v-if="!isAuthenticated">
          <NuxtLink to="/login">Se connecter</NuxtLink>
          <NavLink url="/register">S'inscrire</NavLink>
        </div>
        <div class="flex gap-4 items-center" v-else>
          <NavLink url="/register">Mon compte</NavLink>
        </div>
      </menu>
    </header>
</template>

<style scoped>

</style>