import { createRouter, createWebHistory } from 'vue-router';

import NoteListPage from './pages/NoteListPage.vue';
import CreateNotePage from './pages/CreateNotePage.vue';
import EditNotePage from './pages/EditNotePage.vue';

// List of visible routes
const routes = [
    { path: '/', component: NoteListPage },                            // Main page
    { path: '/notes/create', component: CreateNotePage },               // Note creation page
    { path: '/notes/:id/edit', component: EditNotePage, props: true },  // Note editing page
    { path: '/login', component: () => import('./pages/LoginPage.vue') }, // User login page
    { path: '/register', component: () => import('./pages/RegisterPage.vue') }, // User registration page
];

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Guard for Router
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('authToken');
  if (to.path.startsWith('/notes') && !token) {
    next('/login');
  } else if ((to.path === '/login' || to.path === '/register') && token) {
    next('/');
  } else {
    next();
  }
});

export default router;