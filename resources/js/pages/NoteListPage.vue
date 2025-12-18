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
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import notesApi from '../api/notes'
import authApi from '../api/auth'
import { Notify } from 'quasar'

export default {
  name: 'NoteListPage',

  data () {
    return {
      notes: [],
      deleteDialog: false,
      noteToDelete: null,
      search: '',
      sort: {
        sortBy: 'id',
        descending: false
      },
      // Define columns
      columns: [
        { name: 'id', label: 'ID', field: 'id', sortable: true },
        { name: 'title', label: 'Title', field: 'title', sortable: true },
        { name: 'content', label: 'Content', field: 'content', sortable: true },
        { name: 'created_at', label: 'Created At', field: 'created_at', sortable: true },
        { name: 'updated_at', label: 'Updated At', field: 'updated_at', sortable: true },
        { name: 'actions', label: 'Actions', field: 'actions' }
      ]
    }
  },

  computed: {
    authStore () {
      return useAuthStore()
    },

    isAuthenticated () {
      return this.authStore.isAuthenticated()
    },

    filteredNotes () {
      if (!this.search) return this.notes

      const term = this.search.toLowerCase()

      return this.notes.filter(note =>
        note.title.toLowerCase().includes(term) ||
        note.content.toLowerCase().includes(term) ||
        String(note.id).includes(term)
      )
    }
  },

  methods: {
    // Load notes into table
    async loadNotes () {
      if (!this.isAuthenticated) {
        this.$router.push('/login')
        Notify.create({
          type: 'warning',
          message: 'Please log in',
          position: 'top-right'
        })
        return
      }

      try {
        const response = await notesApi.index()
        this.notes = response.data.data
      } catch (error) {
        if (error.response?.status === 401) {
          this.authStore.logout()
          this.$router.push('/login')
        } else {
          Notify.create({
            type: 'negative',
            message: 'Failed to load notes',
            position: 'top-right'
          })
        }
      }
    },

    // Navigation method for Note Creation page
    goToCreate () {
      this.$router.push('/notes/create')
    },

    // Navigation method for Note Editing page
    editNote (id) {
      this.$router.push(`/notes/${id}/edit`)
    },

    // Delete logic
    openDeleteModal (row) {
      this.noteToDelete = row
      this.deleteDialog = true
    },

    // Confirm Delete logic
    async confirmDelete () {
      await notesApi.delete(this.noteToDelete.id)
      this.deleteDialog = false
      this.loadNotes()
    },

    // Logout logic
    async logout () {
      try {
        await authApi.logout()
      } catch {}

      this.authStore.logout()
      Notify.create({
        type: 'positive',
        message: 'Logged out successfully',
        position: 'top-right'
      })
      this.$router.push('/login')
    }
  },

  mounted () {
    this.loadNotes()
  }
}
</script>

