import './style.css';

import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

import axios from 'axios'
import { VueQueryPlugin } from "@tanstack/vue-query";
import { initializeTheme } from './composables/use-appearance';

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

initializeTheme();

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue', { eager: true })
    return (pages[`./pages/${name}.vue`])!
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(VueQueryPlugin)
      .mount(el)
  },
});
