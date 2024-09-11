

export const useAuth = defineStore(
  "auth",
  () => {
    const user = ref(null);
    const token = ref("");
    const verified = ref(false);

    const isAuthenticated = computed(() => {
        return user.value !== null && verified.value !== false;
    });

    const authenticate = (newUser) => {
      user.value = newUser;
    };

    const logout = () => {
      user.value = null;
      token.value = null;
    };

    return {
      user,
      authenticate,
      token,
      isAuthenticated,
      logout,
        verified
    };
  },
  {
    persist: {
      storage: persistedState.cookiesWithOptions({
        sameSite: "strict",
      }),
    },
  }
);
