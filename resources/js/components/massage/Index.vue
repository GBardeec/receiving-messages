<template>
    <IndexHeader></IndexHeader>
    <div class="container">
        <main>
            <div>
                <div v-if="this.user['is_admin']">
                    432
                </div>
                <div v-else>
                    <h4 class="text-center">
                        Отправка сообщений
                    </h4>

                    <!-- Вывод ошибок -->
                    <div v-if="errors && errors.length > 0" class="alert alert-danger">
                        <p v-for="error in errors" :key="error" class="m-0">{{ error }}</p>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="name" v-model="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Адрес эл. почты</label>
                        <input type="email" class="form-control" id="email" v-model="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="massage" class="form-label">Сообщение</label>
                        <textarea class="form-control" id="massage" v-model="message" required></textarea>
                    </div>
                    <button class="btn btn-primary" @click.prevent="postApplication">
                        Отправить
                    </button>
                </div>
            </div>
        </main>
    </div>

</template>

<script>

import IndexHeader from "@/components/header/Index.vue";

export default {
    components: {IndexHeader},
    name: "IndexMassage",

    data() {
        return {
            user: {},

            name: null,
            email: null,
            message: null,

            errors: [],
        }
    },

    mounted() {
        this.getUser();
    },

    methods: {
        getUser() {
            axios.get('/api/get-user')
                .then(res => {
                    this.user = res.data;

                    this.name = this.user['name'];
                    this.email = this.user['email'];
                })
                .catch(error => {
                    console.error(error);
                });
        },

        postApplication() {
            axios.post('/api/store-application', { name: this.name, email: this.email, message: this.message })
                .then(res => {
                    console.error('success');
                })
                .catch(error => {
                    this.errors = [];

                    if (error.response && error.response.data && error.response.data.errors) {
                        const serverErrors = error.response.data.errors;

                        for (const key in serverErrors) {
                            if (serverErrors.hasOwnProperty(key)) {
                                const errorMessage = "Ошибка: " + serverErrors[key];
                                this.errors.push(errorMessage);
                            }
                        }
                    } else {
                        this.errors.push("Ошибка: " + error.message);
                    }
                });
        }
    },
};
</script>
