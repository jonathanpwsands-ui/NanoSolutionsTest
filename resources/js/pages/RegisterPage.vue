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

        <!-- Cancel button-->
        <q-btn label="Cancel" flat color="grey" to="/login" />
      </div>
    </q-form>
  </div>
</template>

<script>
import authApi from "../api/auth";
import { useAuthStore } from "../stores/auth";
import { Notify } from "quasar";

export default {
  name: "RegisterPage",

  data() {
    return {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      authStore: null
    };
  },

  created() {
    // Initialize Pinia store
    this.authStore = useAuthStore();
  },

  methods: {
    // Register a user
    async register() {
      try {
        const { data } = await authApi.register({
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation
        });

        this.authStore.setAuth(data.user, data.token);
        this.$router.push("/");
      } catch (error) {
        Notify.create({
          type: "negative",
          message:
            error.response?.data?.message || "Registration failed",
          position: "top-right"
        });
      }
    }
  }
};
</script>
