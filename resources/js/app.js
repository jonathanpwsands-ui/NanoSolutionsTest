import { createApp } from 'vue'
import { Quasar } from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/dist/quasar.css'
import router from './router';

const app = createApp({});

app.use(Quasar);
app.use(router);
app.mount('#app');