import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/style.css', 'resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Inline assets smaller than 4 KB directly into the CSS/JS output
        assetsInlineLimit: 4096,
        // Enable CSS code splitting so each entry gets its own minimal CSS file
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                // Keep vendor JS in a separate chunk so it can be cached independently
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
                // Predictable filenames with content hashes for long-lived browser caching
                chunkFileNames:  'js/[name]-[hash].js',
                entryFileNames:  'js/[name]-[hash].js',
                assetFileNames:  'assets/[name]-[hash][extname]',
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
