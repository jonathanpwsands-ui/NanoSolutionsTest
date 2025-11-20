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

  methods: {
    toggleDarkMode() {
      Dark.toggle(); // 🌙 toggles dark/light
    }
  }
};
</script>