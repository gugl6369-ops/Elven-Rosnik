import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import { createApp, h } from 'vue';

import { AppToastHost } from '@/shared/ui/toast';

import '../css/app.css';

const pinia = createPinia();

pinia.use(piniaPluginPersistedstate);

createInertiaApp({
    pages: './pages',
    setup({ el, App, props, plugin }) {
        return createApp({
            render: () => [h(App, props), h(AppToastHost)],
        })
            .use(plugin)
            .use(pinia)
            .mount(el);
    },
});