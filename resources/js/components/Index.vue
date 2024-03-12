<template>
    <div>
        <router-view></router-view>
    </div>
</template>

<script>
import IndexHeader from "@/components/header/Index.vue";
import IndexFooter from "@/components/footer/Index.vue";

export default {
    components: {IndexFooter, IndexHeader},
    name: "Index",

    methods: {

    },
}
</script>

<style scoped>

</style>
