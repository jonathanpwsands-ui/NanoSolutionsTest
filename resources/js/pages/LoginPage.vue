<!-- Login Page -->;
<template>
  <div class="q-pa-md" style="max-width: 600px; margin: auto;">
    <h4 class="q-mb-lg">Login</h4>
    <q-form @submit.prevent="login">
      <!-- Email Address field -->
      <q-input v-model="email" label="Email" filled class="q-mb-md" type="email" required />

      <!-- Password field -->
      <q-input v-model="password" label="Password" filled class="q-mb-md" type="password" required />
      <div class="row q-gutter-sm">

        <!-- Login button -->
        <q-btn label="Login" color="primary" type="submit" />

        <!-- Register button-->
        <q-btn label="Register" flat color="grey" to="/register" />
      </div>
    </q-form>
  </div>
</template>

<script>
import { ref } from "vue";
import { useRouter } from "vue-router";
import authApi from "../api/auth";
import { useAuthStore } from "../stores/auth";
import { Notify } from "quasar";

export default {
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const email = ref("");
    const password = ref("");

    // Log in the user
    const login = async () => {
      try {
        const { data } = await authApi.login({ email: email.value, password: password.value });
        authStore.setAuth(data.user, data.token);
        router.push("/");
      } catch (error) {
        Notify.create({ type: "negative", message: "Invalid credentials", position: "top-right" });
      }
    };

    return { email, password, login };
  }
};
</script>
