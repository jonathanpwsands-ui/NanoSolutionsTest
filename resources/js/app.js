import { createApp } from 'vue'
import { Quasar } from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/dist/quasar.css'

import ExampleComponent from './Components/ExampleComponent.vue'

const app = createApp({
    components: {
        ExampleComponent
    }
})

app.use(Quasar, { config: {}, plugins: {} })
app.mount('#app')