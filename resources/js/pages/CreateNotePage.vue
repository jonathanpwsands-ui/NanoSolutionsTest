<!-- Form for creating a new Note -->
<template>
  <div class="q-pa-md" style="max-width: 600px; margin: auto;">
    <h4 class="q-mb-lg">New Note</h4>

    <q-form @submit.prevent="createNote">
      <!-- Title field -->
      <q-input
        v-model="title"
        label="Title"
        filled
        class="q-mb-md"
        required
      />

      <!-- Content field -->
      <q-input
        v-model="content"
        label="Content"
        type="textarea"
        filled
        class="q-mb-md"
        required
      />

      <!-- Create button -->
      <div class="row q-gutter-sm">
        <q-btn
          label="Create"
          color="primary"
          type="submit"
        />

        <!-- Cancel button -->
        <q-btn
          label="Cancel"
          flat
          color="grey"
          @click="goBack"
        />
      </div>
    </q-form>
  </div>
</template>

<script>
import notesApi from "../api/notes";
import { useAuthStore } from "../stores/auth";
import { Notify } from "quasar";

export default {
  name: "CreateNotePage",

  data() {
    return {
      title: "",
      content: ""
    };
  },

  methods: {
    // Create a new note
    async createNote() {
      const authStore = useAuthStore();

      if (!authStore.isAuthenticated()) {
        this.$router.push("/login");
        Notify.create({
          type: "warning",
          message: "Please log in",
          position: "top-right"
        });
        return;
      }

      try {
        await notesApi.create({
          title: this.title,
          content: this.content
        });
        this.$router.push("/");
      } catch (error) {
        Notify.create({
          type: "negative",
          message: "Failed to create note",
          position: "top-right"
        });
      }
    },

    goBack() {
      this.$router.push("/");
    }
  }
};
</script>
