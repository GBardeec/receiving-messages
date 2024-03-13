<template>
    <div class="container">
        <main>
            <div class="container d-flex justify-content-center align-items-center" style="height: 90vh;">
                <div class="w-50">
                    <div class="text-center mb-3">
                        <h1>Регистрация/Авторизация</h1>
                    </div>

                    <!-- Вывод ошибок -->
                    <div v-if="errors && errors.length > 0" class="alert alert-danger">
                        <p v-for="error in errors" :key="error" class="m-0">{{ error }}</p>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="email" id="email" v-model="email" class="form-control" placeholder="Email">
                        <label class="form-label" for="emailName">Логин</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" id="pass" v-model="password" class="form-control mb-2" placeholder="Пароль">
                        <label class="form-label" for="pass">Пароль</label>

                        <small id="emailHelp" class="form-text text-muted">*Если у вас отсутствует аккаунт - он будет автоматически создан</small>
                        <br>
                        <small id="emailHelp" class="form-text text-muted">**Если Вы уже были на сайте - введите тот же самый логин и пароль</small>
                    </div>
                    <div class="d-grid gap-2 col-6 mx-auto">
                        <button @click.prevent="postAuth" class="btn btn-secondary" style="font-size: 1.3em;" type="submit">
                            Войти
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import router from "@/router.js";

export default {

    name: "IndexAuth",

    data() {
        return {
            auth: false,
            email: null,
            password: null,
            errors: [],
        }
    },

    methods: {
        postAuth() {
            axios.post('/api/auth', { email: this.email, password: this.password })
                .then(res => {
                    localStorage.setItem('token', res.data.token);
                    axios.defaults.headers.common["Authorization"] = `Bearer ${res.data.token}`;

                    if (res.data.status === 'success') {
                        this.auth = true;
                        this.$emit('authenticated', res.data.token);
                        router.push({ name: 'massage.index' });
                    }
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
        },
    },
};
</script>
