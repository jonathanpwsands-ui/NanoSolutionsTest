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
import { ref } from "vue";
import { useRouter } from "vue-router";
import notesApi from "../api/notes";
import { Notify } from "quasar";

export default {
  name: 'CreateNotePage',

  setup() {
    const router = useRouter();

    const title = ref("");
    const content = ref("");

    // Create a new note
    const createNote = async () => {
      try {
        await notesApi.create({
          title: title.value,
          content: content.value,
        });

        router.push("/");
      } catch (error) {
        if (error.response?.status === 422) {
          Notify.create({
            type: "warning",
            message: error.response.data.message ?? "Validation error",
            position: "top-right",
          });
        } else {
          Notify.create({
            type: "negative",
            message: "Failed to create note",
            position: "top-right",
          });
        }
      }
    };

    const goBack = () => {
      router.push("/");
    };

    return {
      title,
      content,
      createNote,
      goBack,
    };
  },
};
</script>
