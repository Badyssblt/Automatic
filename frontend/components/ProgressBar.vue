<script setup>
defineProps({
  width: {
    type: String,
    default: "w-1/2"
  }
})
</script>

<template>
  <div>
    <h2 class="sr-only">Steps</h2>

    <div>
      <div class="overflow-hidden rounded-full bg-gray-200">
        <div
            class="h-2 rounded-full bg-red-500 transition-all duration-300 ease-in-out"
            :class="width"
        ></div>
      </div>

      <ol class="mt-4 grid grid-cols-2 text-sm font-medium text-gray-500">
        <li class="flex items-center justify-start text-red-600 sm:gap-1.5">
          <svg
              class="w-6 h-6 sm:w-5 sm:h-5"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
          >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
            />
          </svg>
          <span class="hidden sm:inline">Informations personnelles</span>
        </li>

        <li class="flex items-center justify-end text-red-600 sm:gap-1.5">
          <span class="hidden sm:inline">Mails</span>
          <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6"
          >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
            />
          </svg>
        </li>
      </ol>
    </div>
  </div>
</template>

<style scoped>
</style>
