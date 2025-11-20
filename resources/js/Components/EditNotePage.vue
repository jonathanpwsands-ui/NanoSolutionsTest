<!-- Form for updating an existing Note -->
<template>
  <div class="q-pa-md" style="max-width: 600px; margin: auto;">
    <h4 class="q-mb-lg">Edit Note</h4>

    <!-- Title field -->
    <q-form @submit.prevent="updateNote">
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
import notesApi from '../api/notes';

export default {
  name: 'NoteEditPage',

  data() {
    return {
      title: '',
      content: '',
    };
  },

  created() {
    this.noteId = this.$route.params.id;
    this.loadNote();
  },

  methods: {
    // Load Note being edited
    async loadNote() {
      try {
        const response = await notesApi.get(this.noteId);
        this.title = response.data.title;
        this.content = response.data.content;
      } catch (error) {
        console.error('Unable to load note:', error);
      }
    },

    // Update Note being edited
    async updateNote() {
      try {
        await notesApi.update(this.noteId, {
          title: this.title,
          content: this.content,
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
