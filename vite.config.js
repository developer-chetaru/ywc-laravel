import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
optimizeDeps: {
  include: ['date-fns', '@melloware/coloris']
},
  server: {
    hmr: { overlay: false }, // enable overlay to show errors in browser
  },
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/forum/livewire-tailwind/css/forum.css',
        'resources/forum/livewire-tailwind/js/forum.js',
      ],
      refresh: true,
    }),
  ],
});
