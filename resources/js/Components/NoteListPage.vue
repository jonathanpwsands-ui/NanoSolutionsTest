<!-- Notes Table -->
<template>
  <q-table
    title="Notes"
    :rows="notes"
    :columns="columns"
    row-key="id"
  />
</template>

<!-- Buttons for each Note -->
<template v-slot:body-cell-actions="props">
  <q-td :props="props">
    <!-- Edit button -->
    <q-btn
      flat
      dense
      color="primary"
      icon="edit"
      @click="editNote(props.row.id)"
    />

    <!-- Delete button -->
    <q-btn
      flat
      dense
      color="negative"
      icon="delete"
      @click="openDeleteModal(props.row)"
    />
  </q-td>
</template>

<script>
import { ref, onMounted } from 'vue';
import notesApi from '../api/notes';

// Add reactive data to the table
const notes = ref([]);

// Load notes into the table
const loadNotes = async () => {
  try {
    const response = await notesApi.list();
    notes.value = response.data;
  } catch (error) {
    console.error('Unable to load notes:', error);
  }
};

// Load notes upon loading the page
onMounted(() => {
  loadNotes();
});

const columns = [
  {
    name: 'id',
    required: true,
    label: 'ID',
    align: 'left',
    field: row => row.name,
    format: val => `${val}`,
    sortable: true
  },
  { name: 'title', label: 'Title', field: 'title' },
  { name: 'content', label: 'Content', field: 'content'},
  { name: 'created_at', label: 'Created At', field: 'created_at' },
  { name: 'updated_at', label: 'Updated At', field: 'updated_at' },
]

export default {
  name: 'NoteListPage',

  setup () {
    return {
      columns,
      rows
    }
  }
}
</script>