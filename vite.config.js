import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                // Frontend modules (optional - for production bundling)
                // Uncomment to enable bundling:
                // 'resources/js/frontend/main.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Output configuration
        rollupOptions: {
            output: {
                // Manual chunks for better caching
                manualChunks: {
                    'vendor': ['jquery', 'bootstrap'],
                }
            }
        },
        // Minification settings
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
            },
        },
    },
});
