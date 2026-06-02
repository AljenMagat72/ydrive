import { defineConfig } from 'vite'

import laravel from 'laravel-vite-plugin'
import { resolve } from 'path'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  root: '../../../',
  cacheDir: resolve(__dirname, 'node_modules/.vite'),
  plugins: [
    laravel({
      input: ['./app.css'],
      buildDirectory: 'build/admin',
      hotFile: '../../../public/hot/admin',
      refresh: true,
    }),
    tailwindcss(),
  ],
})
