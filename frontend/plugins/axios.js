import axios from "axios";
import { useAuth } from "@/stores/auth";
import Cookie from "js-cookie";

export default defineNuxtPlugin((nuxtApp) => {

  const store = useAuth();

  const config = nuxtApp.$config
  const api = axios.create({
    baseURL: config.public.API_URL,
    timeout: 10000,
    headers: {
      'Content-Type': 'application/ld+json'
    }
  });

  api.interceptors.request.use((config) => {
    const token = store?.token;
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    if (config.method === 'patch') {
      config.headers['Content-Type'] = 'application/merge-patch+json';
    }
    return config;
  });

  // Intercepteurs de réponses
  api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response && error.response.status === 401) {
          nuxtApp.$auth?.logout();
        }
        return Promise.reject(error);
      }
  );


  nuxtApp.provide("api", api);
});
