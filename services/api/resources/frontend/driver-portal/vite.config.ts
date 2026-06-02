import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'
import tailwindcss from '@tailwindcss/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';

export default defineConfig({
  root: '../../../',
  cacheDir: resolve(__dirname, 'node_modules/.vite'),
  plugins: [
    wayfinder({
      path: './src/wayfinder',
      command: 'php ../../../artisan wayfinder:generate',
    }),
    laravel({
      input: ['./src/main.ts'],
      buildDirectory: 'build/driver-portal',
      hotFile: '../../../public/hot/driver-portal',
      refresh: true,
    }),
    tailwindcss(),
    vue(),
  ],
  resolve: {
    alias: {
      '@': resolve(__dirname, './src'),
    },
  },
})
