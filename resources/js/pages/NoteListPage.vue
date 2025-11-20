<!-- Note List Page -->
<template>
  <div class="q-pa-md">
    <!-- Add Note button -->
    <q-btn
      label="Add Note"
      color="primary"
      class="q-mb-md"
      @click="goToCreate"
    />

    <q-table
      title="Notes"
      :rows="notes"
      :columns="columns"
      row-key="id"
    >
      <!-- Actions column for each Note -->
      <template v-slot:body-cell-actions="props">
        <q-td :props="props">
          <!-- Edit Note button -->
          <q-btn
            flat dense color="primary"
            icon="edit"
            @click="editNote(props.row.id)"
          />

          <!-- Delete Note button -->
          <q-btn
            flat dense color="negative"
            icon="delete"
            @click="openDeleteModal(props.row)"
          />
        </q-td>
      </template>
    </q-table>
  
  <!-- Delete confirmation dialog -->
    <q-dialog v-model="deleteDialog">
      <q-card>
        <q-card-section class="text-h6">Delete Note?</q-card-section>
        <q-card-section>
          Are you sure you want to delete this note?
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" v-close-popup/>
          <q-btn flat label="Delete" color="negative" @click="confirmDelete"/>
        </q-card-actions>
      </q-card>
    </q-dialog>

  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import notesApi from '../api/notes';

export default {
  name: 'NoteListPage',

  setup() {
    const router = useRouter();

    const notes = ref([]);
    const deleteDialog = ref(false);
    const noteToDelete = ref(null);

    // Define columns for Notes table
    const columns = [
      { name: 'id', label: 'ID', field: 'id', sortable: true },
      { name: 'title', label: 'Title', field: 'title' },
      { name: 'content', label: 'Content', field: 'content' },
      { name: 'created_at', label: 'Created At', field: 'created_at' },
      { name: 'updated_at', label: 'Updated At', field: 'updated_at' },
      { name: 'actions', label: 'Actions', field: 'actions' }
    ];

    // Load notes into the table
    const loadNotes = async () => {
      const response = await notesApi.list();
      notes.value = response.data;
    };

    // Go to Create Note page
    const goToCreate = () => {
      router.push('/notes/create');
    };

    // Go to Edit Note page
    const editNote = (id) => {
      router.push(`/notes/${id}/edit`);
    };

    // Open Delete Note dialog
    const openDeleteModal = (row) => {
      noteToDelete.value = row;
      deleteDialog.value = true;
    };

    // Confirm deletion of Note
    const confirmDelete = async () => {
      await notesApi.delete(noteToDelete.value.id);
      deleteDialog.value = false;
      loadNotes();
    };

    // Load notes upon loading the page
    onMounted(loadNotes);

    return {
      notes,
      columns,
      deleteDialog,
      openDeleteModal,
      confirmDelete,
      editNote,
      goToCreate
    };
  }
};
</script>