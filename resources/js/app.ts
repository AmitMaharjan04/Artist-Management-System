import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { createPinia } from "pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";

import '@fortawesome/fontawesome-free/css/all.min.css';
import "tabulator-tables/dist/css/tabulator.min.css";
import 'sweetalert2/dist/sweetalert2.min.css';

const app = createApp(App);
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

app.provide("router", router);
app.use(router);
app.use(pinia);

app.mount("#app");
