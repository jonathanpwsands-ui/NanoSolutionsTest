<!-- Registration Page for registering a new user -->
<template>
  <div class="q-pa-md" style="max-width: 600px; margin: auto;">
    <h4 class="q-mb-lg">Register</h4>
    <q-form @submit.prevent="register">
      <!-- Name field -->
      <q-input v-model="name" label="Name" filled class="q-mb-md" required />

      <!-- Email Address field -->
      <q-input v-model="email" label="Email" filled class="q-mb-md" type="email" required />

      <!-- Password field -->
      <q-input v-model="password" label="Password" filled class="q-mb-md" type="password" :rules="['min:6']" required />

      <!-- Paswword Confirmation field -->
      <q-input v-model="password_confirmation" label="Confirm Password" filled type="password" :rules="['match:password']" required />
      <div class="row q-gutter-sm">

        <!-- Register button -->
        <q-btn label="Register" color="primary" type="submit" />

        <!-- Login button-->
        <q-btn label="Login" flat color="grey" to="/login" />
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
    const name = ref(""); const email = ref(""); const password = ref(""); const password_confirmation = ref("");

    // Register a user
    const register = async () => {
      try {
        const { data } = await authApi.register({ name: name.value, email: email.value, password: password.value, password_confirmation: password_confirmation.value });
        authStore.setAuth(data.user, data.token);
        router.push("/");
      } catch (error) {
        Notify.create({ type: "negative", message: error.response?.data?.message || "Registration failed", position: "top-right" });
      }
    };

    return { name, email, password, password_confirmation, register };
  }
};
</script>
