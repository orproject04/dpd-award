import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import { viteStaticCopy } from "vite-plugin-static-copy";

export default defineConfig({
    // server: {
    //     host: "0.0.0.0",
    //     port: 5173,
    //     strictPort: true,
    //     origin: "http://172.16.88.82:5173",
    //     cors: true,
    // },
    // server: {
    //     host: "0.0.0.0", // penting! supaya bisa diakses dari container
    //     cors: true,
    //     port: parseInt(process.env.VITE_PORT) || 5173,
    //     hmr: {
    //         host: process.env.VITE_HOST || "192.168.1.10",
    //     },
    // },
    plugins: [
        tailwindcss(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: "resources/fonts",
                    dest: "", // will copy to /public/build/fonts
                },
            ],
        }),
    ],
    assetsInclude: ["**/*.png", "**/*.jpg", "**/*.svg", "**/*.jpeg"],
});
