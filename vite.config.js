import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { quasar } from '@quasar/vite-plugin'
import { resolve } from 'path'

export default defineConfig({
    server: {
        host: true,
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
    },
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        quasar({
            sassVariables: resolve(__dirname, 'resources/css/quasar-variables.sass')
        })
    ]
})
