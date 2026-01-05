import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/public/theme.css',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        // Production optimizations
        minify: 'esbuild',
        sourcemap: false,
        // Code splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios'],
                },
                // Asset file naming for cache busting
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },
        // Chunk size warnings
        chunkSizeWarningLimit: 500,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['axios'],
    },
});
