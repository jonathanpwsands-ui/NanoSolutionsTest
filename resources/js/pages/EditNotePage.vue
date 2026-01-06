<!-- Form for updating an existing Note -->
<template>
  <div class="q-pa-md" style="max-width: 600px; margin: auto;">
    <h4 class="q-mb-lg">Edit Note</h4>

    <q-form @submit.prevent="updateNote">
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

      <!-- Update button -->
      <div class="row q-gutter-sm">
        <q-btn
          label="Update"
          color="primary"
          @click="updateNote"
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
  name: "NoteEditPage",

  data() {
    return {
      title: "",
      content: "",
      noteId: this.$route.params.id
    };
  },

  methods: {
    // Load the note to edit
    async loadNote() {
      const authStore = useAuthStore();

      if (!authStore.isAuthenticated()) {
        this.$router.push("/login");
        return;
      }

      try {
        const response = await notesApi.get(this.noteId);
        this.title = response.data.title;
        this.content = response.data.content;
      } catch (error) {
        Notify.create({
          type: "negative",
          message: "Failed to load note"
        });
      }
    },

    async updateNote() {
      const authStore = useAuthStore();

      if (!authStore.isAuthenticated()) {
        this.$router.push("/login");
        return;
      }

      try {
        await notesApi.update(this.noteId, {
          title: this.title,
          content: this.content
        });

        Notify.create({
          type: "positive",
          message: "Note updated successfully"
        });

        this.$router.push("/");
      } catch (error) {
        Notify.create({
          type: "negative",
          message: "Failed to update note",
          position: "top-right"
        });
      }
    },

    // Return to Note List
    goBack() {
      this.$router.push({ path: "/", force: true });
    }
  },

  mounted() {
    this.loadNote();
  }
};
</script>