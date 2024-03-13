<template>
    <IndexHeader></IndexHeader>
    <div class="container">
        <main>
            <div>
                <div v-if="this.user['is_admin']">
                    <div class="d-flex">
                        <div class="form-check mr-2">
                            <input class="form-check-input" type="checkbox" v-model="isNotActive"
                                   @change="updateURLParams">
                            <label class="form-check-label" for="flexCheckDefault">
                                Показать неактивные заявки
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="orderByDeskDate"
                                   @change="updateURLParams">
                            <label class="form-check-label" for="flexCheckChecked">
                                Показать вначале старые заявки
                            </label>
                        </div>
                    </div>
                    <div>
                        <!-- Вывод ошибок -->
                        <div v-if="errors && errors.length > 0" class="alert alert-danger">
                            <p v-for="error in errors" :key="error" class="m-0">{{ error }}</p>
                        </div>

                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Эл.почта</th>
                                <th scope="col">Сообщение</th>
                                <th scope="col">Дата</th>
                                <th scope="col">Действия</th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in application" :key="item.id">
                            <tr>
                                <th scope="row">{{ index + 1 }}</th>
                                <td>{{ item.user.name }}</td>
                                <td>{{ item.user.email }}</td>
                                <td>{{ item.message }}</td>
                                <td>{{ formatDate(item.created_at) }}</td>
                                <td>
                                    <a class="btn btn-success" @click="toggleCommentInput(index)" v-if="item.comment === null">
                                        Ответить
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="showCommentInput === index">
                                <td colspan="6" class="p-0">
                                    <div class="input-group mt-1 mb-1">
                                        <input type="text" class="form-control w-75"
                                               placeholder="Введите комментарий"
                                               aria-describedby="basic-addon2"
                                               v-model="commentInput">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" @click="putApplication(index)">
                                                Отпрвить
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
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

            application: null,
            isNotActive: false,
            orderByDeskDate: false,

            showCommentInput: null,
            commentInput: '',
            commentIdInput: '',

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

                    if (this.user.is_admin) {
                        this.getApplication();
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },

        postApplication() {
            axios.post('/api/requests', {name: this.name, email: this.email, message: this.message})
                .then(res => {
                    console.log('success');
                    this.message = null;
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

        updateURLParams() {
            let queryString = '';

            if (this.isNotActive) {
                queryString += 'isNotActive&';
            }

            if (this.orderByDeskDate) {
                queryString += 'orderByDeskDate&';
            }

            if (queryString.length > 0) {
                queryString = '?' + queryString.slice(0, -1);
            }

            history.pushState({}, null, window.location.pathname + queryString);

            this.getApplication();
        },

        getApplication() {
            axios.get('/api/requests', {
                params: {
                    isNotActive: this.isNotActive,
                    orderByDeskDate: this.orderByDeskDate
                }
            })
                .then(res => {
                    this.application = res.data.data.application;
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

        formatDate(dateString) {
            return new Date(dateString).toLocaleString();
        },

        toggleCommentInput(index) {
            this.showCommentInput = this.showCommentInput === index ? null : index;
        },

        putApplication(index) {
            axios.put('/api/requests', {
                params: {
                    id: this.application[index].id,
                    comment: this.commentInput
                }
            })
                .then(res => {

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

            this.commentInput = '';
            this.showCommentInput = null;
            this.getApplication()
        },
    },
};
</script>
