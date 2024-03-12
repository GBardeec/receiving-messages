import { createApp } from 'vue';
import Index from "@/components/Index.vue";
import './bootstrap';
import axios from 'axios';
import router from './router';

const app = createApp({
    el: '#app',
    components: {
        Index,
    }
});

if (localStorage.getItem('token')) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${localStorage.getItem("token")}`;
}

app.use(router);

app.mount('#app');
