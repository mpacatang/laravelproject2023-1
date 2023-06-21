import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.scss',
            'resources/js/app.ts',
        ]),

    ],
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    }
});