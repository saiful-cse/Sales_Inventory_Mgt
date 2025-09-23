import { router } from '@inertiajs/vue3'
import './bootstrap'

// CSS
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'vue3-easy-data-table/dist/style.css'
import 'nprogress/nprogress.css'
import './Assets/css/main.css'

// Vue & Inertia
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

// Plugins
import Vue3EasyDataTable from 'vue3-easy-data-table'
import NProgress from 'nprogress'

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        app.use(plugin)
        app.component('EasyDataTable', Vue3EasyDataTable)
        app.mount(el)
    },
})

// NProgress integration with Inertia router
router.on('start', () => NProgress.start())
router.on('finish', () => NProgress.done())
