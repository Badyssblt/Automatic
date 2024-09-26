<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  visible: Boolean
});

const showMenu = ref(props.visible);
const activeContent = ref('mails');

const isMenuHovered = ref(false);
const isContentHovered = ref(false);

watch(() => props.visible, (newVal) => {
  showMenu.value = newVal;
  if (newVal) {
    activeContent.value = 'mails';
  }
});

const setActiveContent = (content) => {
  activeContent.value = content;
};

const clearActiveContent = () => {
  if (!isMenuHovered.value && !isContentHovered.value) {
    activeContent.value = null;
  }
};
</script>

<template>
  <div v-if="showMenu" class="bg-white shadow-md p-4 w-[700px] flex"
       @mouseenter="isMenuHovered = true" @mouseleave="clearActiveContent">
    <div class="flex flex-col w-1/2 border-r">
      <div @mouseenter="setActiveContent('mails'); isMenuHovered = true"
           @mouseleave="isMenuHovered = false" class="cursor-pointer py-2">
        <p class="text-lg w-full">Mails</p>
      </div>
      <div @mouseenter="setActiveContent('bdd'); isMenuHovered = true"
           @mouseleave="isMenuHovered = false" class="cursor-pointer py-2">
        <p class="text-nowrap text-lg">Base de donnée</p>
      </div>
      <div @mouseenter="setActiveContent('deploy'); isMenuHovered = true"
           @mouseleave="isMenuHovered = false" class="cursor-pointer py-2">
        <p class="text-lg">Déploiement</p>
      </div>
    </div>
    <div class="ml-4 w-full"
         @mouseenter="isContentHovered = true" @mouseleave="isContentHovered = false">
      <p v-if="activeContent === 'mails'" class="flex flex-col items-center">
        Démarrer l'envoi de vos mails facilement avec notre API. Vous pouvez envoyer des données personnalisées et les styliser avec TailwindCSS
        <NavLink class="w-fit mt-4" to="/mails">Commencer</NavLink>
      </p>
      <p v-if="activeContent === 'bdd'" class="flex flex-col items-center">
        Gérer vos bases de données, en important ou exportant vos données dans différents formats (Excel, JSON ect...)
        <NavLink class="w-fit mt-4" to="/databases">Commencer</NavLink>
      </p>
      <p v-if="activeContent === 'deploy'">Voici le contenu pour déploiement</p>
    </div>
  </div>
</template>


