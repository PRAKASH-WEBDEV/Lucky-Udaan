import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/site.scss',
                'resources/js/site.js',
                'resources/scss/admin.scss',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'mixed-decls', 'legacy-js-api', 'if-function'],
            },
        },
    },
    build: {
        chunkSizeWarningLimit: 900,
    },
    server: {
        watch: { ignored: ['**/storage/framework/views/**'] },
    },
});
