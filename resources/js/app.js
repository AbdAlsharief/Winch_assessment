import './bootstrap';
import { createApp } from 'vue';
import OrdersDashboard from './components/OrdersDashboard.vue';

const app = createApp({});

app.component('orders-dashboard', OrdersDashboard);

app.mount('#app');
