<template>
    <IndexHeader></IndexHeader>
    <div class="container">
        <main>
            <div>
                <div v-if="this.user['is_admin']">
                    432
                </div>
                <div v-else>
                    123
                </div>
            </div>
        </main>
    </div>


</template>

<script>

import IndexHeader from "@/components/header/Index.vue";
import IndexFooter from "@/components/footer/Index.vue";

export default {
    components: {IndexFooter, IndexHeader},
    name: "IndexMassage",

    data() {
        return {
            user: {},
            value: 0,
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
                    console.log(this.user['is_admin']);
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
};
</script>
