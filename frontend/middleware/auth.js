import { useAuth } from "@/stores/auth.js";

export default defineNuxtRouteMiddleware((to, from) => {

    const store = useAuth();

    if (store.isAuthenticated === false) {
        return navigateTo('/login')
    }
})