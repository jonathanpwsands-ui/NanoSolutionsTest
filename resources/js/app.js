import { createApp } from 'vue';
import { Quasar } from 'quasar';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

import 'quasar/src/css/index.sass';
import '@quasar/extras/material-icons/material-icons.css';

import router from './router';
import App from './App.vue';

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

const app = createApp(App);
app.use(pinia);
app.use(Quasar);
app.use(router);

app.mount('#app');
