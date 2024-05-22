import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import { createApp } from 'vue/dist/vue.esm-bundler.js';
import router from './router';
import Login from './components/pages/auth/Login.vue';


const app = createApp({});

app.use(router)
app.component('Login', Login);
app.mount('#app');