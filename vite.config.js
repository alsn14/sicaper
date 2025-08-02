import { resolve } from 'path';

/** @type {import('vite').UserConfig} */
export default {
    plugins: [],
    build: {
        assetsDir: '',
        rollupOptions: {
            input: {
                main: resolve(__dirname, 'scripts.js'),
                dark: resolve(__dirname, 'dark-mode.css'),
                light: resolve(__dirname, 'light-mode.css'),
                style: resolve(__dirname, 'styles.css'),
            },
            output: {
                assetFileNames: '[name][extname]',
                entryFileNames: '[name].js',
            },
        },
    },
};
