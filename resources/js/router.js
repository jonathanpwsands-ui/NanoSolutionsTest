import { createRouter, createWebHistory } from 'vue-router';

import NoteListPage from './pages/NoteListPage.vue';
import CreateNotePage from './pages/CreateNotePage.vue';
import EditNotePage from './pages/EditNotePage.vue';

// List of visible routes
const routes = [
    { path: '/', component: NoteListPage },                            // Main page
    { path: '/notes/create', component: CreateNotePage },               // Note creation page
    { path: '/notes/:id/edit', component: EditNotePage, props: true },  // Note editing page
];

export default createRouter({
    history: createWebHistory(),
    routes,
});