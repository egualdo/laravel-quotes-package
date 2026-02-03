import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        vue({
            template: {
                compilerOptions: {
                    // Configuración para Vue 3
                }
            }
        })
    ],
    build: {
        outDir: 'resources/dist',
        emptyOutDir: true,
        lib: {
            entry: resolve(__dirname, 'resources/js/app.ts'),
            name: 'QuotesUI',
            fileName: (format) => `quotes-ui.${format}.js`
        },
        rollupOptions: {
            external: ['vue'],
            output: {
                globals: {
                    vue: 'Vue'
                },
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'style.css') return 'quotes-ui.css';
                    return assetInfo.name;
                }
            }
        }
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            'vue': 'vue/dist/vue.esm-bundler.js'
        }
    },
    server: {
        port: 3000,
        hmr: {
            host: 'localhost'
        }
    }
});