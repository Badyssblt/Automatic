// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],

  runtimeConfig: {
    public: {
      API_URL: "http://localhost:8215",
      // API_URL: "http://automatic.badyssblilita.fr/v1"
    }
  },

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },

  modules: [
      '@pinia/nuxt',
    '@pinia-plugin-persistedstate/nuxt'
  ]
})