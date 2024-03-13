import { createWebHistory, createRouter } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('./components/massage/Index.vue'),
        name: 'massage.index'
    },
    {
        path: '/auth',
        component: () => import('./components/auth/Index.vue'),
        name: 'auth.index'
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token");

    if (!token) {
        if (to.name !== 'auth.index') {
            return next({ name: 'auth.index' });
        }
    } else {
        if (to.name === 'auth.index') {
            return next({ name: 'massage.index' });
        }
    }

    next();
});

export default router;
