import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token') || null);

  const setAuth = (newUser, newToken) => {
    user.value = newUser;
    token.value = newToken;
    localStorage.setItem('authToken', newToken);
  };

  const logout = () => {
    user.value = null;
    token.value = null;
    localStorage.removeItem('authToken');
  };

  const isAuthenticated = () => !!token.value;

  if (localStorage.getItem('authToken')) {
    token.value = localStorage.getItem('authToken');
  };

  return { user, token, setAuth, logout, isAuthenticated };
}, {
  persist: true  // Requires pinia-plugin-persistedstate
});
