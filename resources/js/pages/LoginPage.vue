<!-- Login Page -->
<template>
  <div class="q-pa-md" style="max-width: 600px; margin: auto;">
    <h4 class="q-mb-lg">Login</h4>

    <q-form @submit.prevent="login">
      <!-- Email Address field -->
      <q-input
        v-model="email"
        label="Email"
        filled
        class="q-mb-md"
        type="email"
        required
      />

      <!-- Password field -->
      <q-input
        v-model="password"
        label="Password"
        filled
        class="q-mb-md"
        type="password"
        required
      />

      <div class="row q-gutter-sm">
        <!-- Login button -->
        <q-btn label="Login" color="primary" type="submit" />

        <!-- Register button -->
        <q-btn label="Register" flat color="grey" to="/register" />
      </div>
    </q-form>
  </div>
</template>

<script>
import authApi from "../api/auth";
import { useAuthStore } from "../stores/auth";
import { Notify } from "quasar";

export default {
  name: "LoginPage",

  data() {
    return {
      email: "",
      password: ""
    };
  },

  methods: {
    // Log in the user
    async login() {
      const authStore = useAuthStore();

      try {
        const { data } = await authApi.login({
          email: this.email,
          password: this.password
        });

        authStore.setAuth(data.user, data.token);
        this.$router.push("/");
      } catch (error) {
        Notify.create({
          type: "negative",
          message: "Invalid credentials",
          position: "top-right"
        });
      }
    }
  }
};
</script>