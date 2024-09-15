import axios from "axios";
import { useAuth } from "@/stores/auth";
import {jwtDecode} from "jwt-decode";

export default defineNuxtPlugin((nuxtApp) => {
  const store = useAuth();
  const config = nuxtApp.$config;

  const api = axios.create({
    baseURL: config.public.API_URL,
    timeout: 10000,
    headers: {
      'Content-Type': 'application/ld+json'
    }
  });

  const isTokenValid = (token) => {
    try {
      const decoded = jwtDecode(token);
      return decoded.exp * 1000 > Date.now();
    } catch (e) {
      return false;
    }
  };

  api.interceptors.request.use((config) => {
    const token = store?.token;
    if (token && isTokenValid(token) && store.isAuthenticated) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    if (config.method === 'patch') {
      config.headers['Content-Type'] = 'application/merge-patch+json';
    }
    return config;
  });

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
