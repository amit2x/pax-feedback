import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/public.js',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        sourcemap: false,
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['jquery', 'bootstrap', 'axios'],
                },
            },
        },
    },
});
