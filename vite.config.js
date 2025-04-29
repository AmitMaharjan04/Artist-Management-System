import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import dotenv from 'dotenv';
import path from "path";
import { fileURLToPath } from "url";

dotenv.config();

const isLocal = process.env.VITE_LOCAL === "true";
const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                "resources/css/app.scss",
                "resources/js/app.ts",
                "resources/css/style.scss",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
            "@": path.resolve(__dirname, "resources/js"),
            "@assets": path.resolve(__dirname, "resources/assets"),
            "@css": path.resolve(__dirname, "resources/css"),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                api: "modern-compiler", // or "modern"
            },
        },
    },
    server: isLocal ? {
        host: "0.0.0.0", // Allow Vite to listen on all IP addresses
        port: 5174, // Default port for Vite
        hmr: {
            host: "0.0.0.0", // The host that your browser can access
            port: 5174,
        },
        watch: {
            usePolling: false,
        },
    } : {},
});