import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from "url";
import cssInjectedByJsPlugin from "vite-plugin-css-injected-by-js";

export default defineConfig({
  server: {
    host: true,
    port: 5173
  },
  plugins: [vue({
    template: {
        transformAssetUrls: {
            base: null,
            includeAbsolute: false,
        },
    },
}
), 

cssInjectedByJsPlugin()],
base: '/',
  build: {
    manifest: true,
    rollupOptions: {
      input: '/src/main.js',
      output: {
        entryFileNames: "[name].js",
        manualChunks: undefined,
      },
      
    },
    chunkSizeWarningLimit: 1500 
  },
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    }
  },
  
})
