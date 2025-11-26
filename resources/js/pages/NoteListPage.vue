<!-- Note List Page -->
<template>
  <div class="q-pa-md">
    <div class="row q-gutter-sm q-mb-md">
      <!-- Add Note button -->
      <q-btn
        label="Add Note"
        color="primary"
        @click="goToCreate" />
      
      <!-- Logout button-->
      <q-btn 
        v-if="isAuthenticated"
        label="Logout"
        color="negative"
        @click="logout" />
    </div>

    <!-- Search bar -->
    <q-input
      v-model="search"
      filled
      placeholder="Search notes…"
      class="q-mb-md"
      debounce="200"
      clearable
      prefix="🔍"
    />

    <q-table
      title="Notes"
      :rows="filteredNotes"
      :columns="columns"
      row-key="id"
      :sort="sort"
      @update:sort="val => sort.value = val"
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
          <q-btn flat label="Cancel" color="grey" v-close-popup />
          <q-btn flat label="Delete" color="negative" @click="confirmDelete" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import notesApi from '../api/notes';
import authApi from '../api/auth.js';
import { Notify } from "quasar";


export default {
  name: 'NoteListPage',

  setup() {
    const router = useRouter();

    const notes = ref([]);
    const deleteDialog = ref(false);
    const noteToDelete = ref(null);

    const search = ref("");

    const sort = ref({
      sortBy: 'id',
      descending: false,
    });

    const authStore = useAuthStore();
    const isAuthenticated = computed(() => authStore.isAuthenticated());

    // Logout logic
    const logout = async () => {
      try {
        await authApi.logout();
      } catch {}
      authStore.logout();
      Notify.create({ type: "positive", message: "Logged out successfully", position: "top-right" });
      router.push('/login');
    };

    // Define computed filter for search
    const filteredNotes = computed(() => {
      if (!search.value) return notes.value;

      const term = search.value.toLowerCase();

      return notes.value.filter(note =>
        note.title.toLowerCase().includes(term) ||
        note.content.toLowerCase().includes(term) ||
        String(note.id).includes(term)
      );
    });

     // Define columns
    const columns = [
      { name: 'id', label: 'ID', field: 'id', sortable: true },
      { name: 'title', label: 'Title', field: 'title', sortable: true },
      { name: 'content', label: 'Content', field: 'content', sortable: true },
      { name: 'created_at', label: 'Created At', field: 'created_at', sortable: true },
      { name: 'updated_at', label: 'Updated At', field: 'updated_at', sortable: true },
      { name: 'actions', label: 'Actions', field: 'actions' }
    ];

    // Load notes into table
    const loadNotes = async () => {
      console.log('loadNotes called');
      console.log('isAuthenticated:', authStore.isAuthenticated());
      console.log('token:', localStorage.getItem('authToken'));
      if (!authStore.isAuthenticated()) {
        console.log('Not authenticated, redirecting to login');
        router.push('/login');
        Notify.create({
          type: "warning",
          message: "Please log in",
          position: "top-right"
        });
        return;
      }
      console.log('Calling notesApi.index()');
      try {
        const response = await notesApi.index();
        console.log('Notes loaded:', response.data);
        notes.value = response.data;
      } catch (error) {
        console.error('Load notes error:', error.response || error);
        if (error.response?.status === 401) {
          authStore.logout();
          router.push('/login');
        } else {
          Notify.create({
            type: "negative",
            message: "Failed to load notes",
            position: "top-right"
          });
        }
      }
    };

    // Navigation functions
    const goToCreate = () => router.push('/notes/create');
    const editNote = id => router.push(`/notes/${id}/edit`);

    // Delete logic
    const openDeleteModal = row => {
      noteToDelete.value = row;
      deleteDialog.value = true;
    };

    // Confirm Delete logic
    const confirmDelete = async () => {
      await notesApi.delete(noteToDelete.value.id);
      deleteDialog.value = false;
      loadNotes();
    };

    console.log('NoteListPage onMounted');
    onMounted(() => {
      loadNotes();
    });

    return {
      notes,
      columns,
      search,
      filteredNotes,
      deleteDialog,
      openDeleteModal,
      confirmDelete,
      editNote,
      goToCreate,
      isAuthenticated,
      logout,
    };
  }
};
</script>
