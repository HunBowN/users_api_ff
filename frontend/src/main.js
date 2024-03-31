import { createApp } from 'vue'
import router from './router.js'
import App from './App.vue'
import PrimeVue from 'primevue/config';


import "primevue/resources/themes/aura-light-green/theme.css";
import "primeflex/primeflex.css";
import 'primeicons/primeicons.css'

import Button from "primevue/button"
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Sidebar from 'primevue/sidebar';
import Message from 'primevue/message';
import Paginator from 'primevue/paginator';
import ProgressSpinner from 'primevue/progressspinner';
import Tooltip from 'primevue/tooltip';


const app = createApp(App)

app.directive('tooltip', Tooltip);

app.component('Sidebar', Sidebar);
app.component('Button', Button);
app.component('DataTable', DataTable);
app.component('Column', Column);
app.component('Paginator', Paginator);
app.component('Message', Message);
app.component('ProgressSpinner', ProgressSpinner);

app.use(PrimeVue).use(router).mount('#app')
