import { createRouter, createWebHistory } from 'vue-router';

import NotesListPage from './pages/NotesListPage.vue';
import NoteCreatePage from './pages/NoteCreatePage.vue';
import NoteEditPage from './pages/NoteEditPage.vue';

// List of visible routes
const routes = [
    { path: '/', component: NotesListPage },                            // Main page
    { path: '/notes/create', component: NoteCreatePage },               // Note creation page
    { path: '/notes/:id/edit', component: NoteEditPage, props: true },  // Note editing page
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
