<!-- Form for creating a new Note -->
<template>
  <div class="q-pa-md" style="max-width: 600px; margin: auto;">
    <h4 class="q-mb-lg">New Note</h4>

    <!-- Title field -->
    <q-form @submit.prevent="createNote">
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
import { Notify } from 'quasar';
import notesApi from '../api/notes';

export default {
  name: 'CreateNotePage',

  data() {
    return {
      title: '',
      content: '',
    };
  },

  methods: {
    // Create a new Note
    async createNote() {
      try {
        await notesApi.create({
          title: this.title,
          content: this.content,
        });

        // Notification for successfully creating a Note
        Notify.create({
          type: 'positive',
          message: 'Note created successfully!',
          timeout: 1500
        });

        this.$router.push('/');
      } catch (error) {
        if (error.response?.status === 422) {
          Notify.create({
            type: 'warning',
            message: error.response.data.message ?? 'Validation failed!',
            position: 'top-right',
            timeout: 1500
          });
        } else {
          Notify.create({
            type: 'negative',
            message: 'Unexpected error occurred. Note creation failed!',
            position: 'top-right',
            timeout: 1500
          });
        }
      }
    },

    // Return to Note List page
    goBack() {
      this.$router.push('/');
    }
  }
};
</script>
