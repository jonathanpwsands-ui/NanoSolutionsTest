<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-toolbar-title>Notes App</q-toolbar-title>

        <!-- Dark Mode Toggle -->
        <q-btn
          flat
          round
          dense
          icon="dark_mode"
          @click="toggleDarkMode"
        />
      </q-toolbar>
    </q-header>

    <!-- Content -->
    <q-page-container>
      <router-view />
    </q-page-container>

  </q-layout>
</template>

<script>
import { Dark } from 'quasar';



export default {
  name: 'App',

  created() {
    const saved = localStorage.getItem("darkMode");
    if (saved) {
      if (saved === "true") Dark.set(true);
      else Dark.set(false);
    }
  },

  methods: {
    toggleDarkMode() {
      Dark.toggle(); // 🌙 toggles dark/light
      localStorage.setItem("darkMode", Dark.isActive);
    }
  }
};
</script>