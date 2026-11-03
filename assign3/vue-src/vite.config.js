import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [vue()],
    base: "./", // relative URLs, so it works in Apache
    build: {
        // place dist alongside handwritten js so the Actions can copy them all in one go
        outDir: "../scripts/vue",
        emptyOutDir: true,
        rollupOptions: {
            input: "src/enquire2.js",
            output: {
                // fixed names so i can ref them in html
                // rollup would typically give them a hash that's problematic integrating 
                // it with my artisinally written HTML
                entryFileNames: "enquire2.js",
                chunkFileNames: "[name].js",
                assetFileNames: "[name][extname]"
            }
        }
    }
});