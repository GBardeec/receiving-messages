<template>
    <div class="container">
        <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
            <h1 class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                <span class="fs-4">Отправка сообщений</span>
            </h1>
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <button type="button" class="btn btn-outline-primary" @click.prevent="logout">
                        Выйти
                    </button>
                </li>
            </ul>
        </header>
    </div>
</template>


<script>
import router from "@/router.js";

export default {
    name: "IndexHeader",

    data() {
        return {
        }
    },

    methods: {
        logout() {
            axios.get('/api/auth/logout')
                .then(res => {
                    localStorage.removeItem('token');
                    router.push({ name: 'auth.index' });
                })
                .catch(error => {
                    console.error(error);
                });
        }
    }
};

</script>
<style scoped>

</style>
