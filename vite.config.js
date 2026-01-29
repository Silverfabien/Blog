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
                user: "./assets/styles/js/security/user.js",
                navbar: "./assets/styles/js/layouts/navbar.js",
                article_editor: "./assets/styles/js/article/article_editor.js",
                comment_editor: "./assets/styles/js/article/comment_editor.js",
                avatar: "./assets/styles/js/avatar/avatar.js",
                article_like: "./assets/styles/js/article/article_like.js",
                article_image: "./assets/styles/js/article/article_image.js",
                confirmation_account: "./assets/styles/js/security/confirmation_account.js"
            },
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.includes('fa-')) {
                        return 'webfonts/[name][extname]'
                    }

                    return 'assets/[name]-[hash][extname]'
                }
            }
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
