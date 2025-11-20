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
console.log("NOTES API CONTENT:", notesApi);
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import notesApi from "../api/notes";
import { Notify } from "quasar";

export default {
  name: "NoteEditPage",

  setup() {
    const router = useRouter();
    const route = useRoute();

    const title = ref("");
    const content = ref("");
    const noteId = ref(route.params.id);

    // Load the note to edit
    const loadNote = async () => {
      const response = await notesApi.get(noteId.value);
      title.value = response.data.title;
      content.value = response.data.content;
    };
    
    // Update the note
    const updateNote = async () => {
      console.log("UPDATE FIRING");
      console.log("notesApi in update()", notesApi);
      console.log("notesApi.update =", notesApi.update);


      try {
        await notesApi.update(noteId.value, {
          title: title.value,
          content: content.value
        });
        console.log("NAVIGATING TO /");
        router.push("/"); 
      } catch (error) {
        console.error("AXIOS ERROR:", error);
        Notify.create({
          type: "negative",
          message: "Failed to update note",
          position: "top-right",
          timeout: 1500,
        });
      }
    };

    // Return to Note List
    const goBack = () => {
      router.push({ path: '/', force: true });
    };

    onMounted(loadNote);

    return {
      title,
      content,
      updateNote,
      goBack
    };
  }
};
</script>
