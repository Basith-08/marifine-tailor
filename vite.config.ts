import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'

const isDev = process.env.NODE_ENV === 'development'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
            detectTls: true,
            buildDirectory: 'build',
        }),

        tailwindcss(),

        isDev
            ? (await import('@laravel/vite-plugin-wayfinder')).wayfinder({
                formVariants: true,
            })
            : null,

        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ].filter(Boolean),
})