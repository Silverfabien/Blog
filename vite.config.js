import { defineConfig } from "vite";
import symfony from "vite-plugin-symfony";
import path from 'path';

export default defineConfig({
    plugins: [
        symfony(
            {
                refresh: true,
            }
        ),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: {
                app: "./assets/styles/js/app.js",
                security: "./assets/styles/js/security/security.js",
                logout: "./assets/styles/js/security/logout.js",
                check_token: "./assets/styles/js/security/check_token.js",
            },
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './assets'),
        },
    },
    server: {
        port: 3000,
    },
});
