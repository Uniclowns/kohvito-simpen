import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // hmr.host: URL yang dipakai BROWSER (via port mapping 5173 host),
        // bukan alamat bind di dalam container.
        hmr: { host: 'localhost' },
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
            // Di Docker (bind mount Windows) inotify tidak jalan → polling.
            usePolling: !!process.env.VITE_POLLING,
        },
    },
});
