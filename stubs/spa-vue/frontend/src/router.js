import { createRouter, createWebHistory } from 'vue-router';
import Home from './pages/Home.vue';
import Catalog from './pages/Catalog.vue';
import Category from './pages/Category.vue';
import Product from './pages/Product.vue';
import Cart from './pages/Cart.vue';
import Checkout from './pages/Checkout.vue';
import Success from './pages/Success.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import Account from './pages/Account.vue';

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home },
        { path: '/catalog', component: Catalog },
        { path: '/category/:slug', component: Category },
        { path: '/product/:slug', component: Product },
        { path: '/cart', component: Cart },
        { path: '/checkout', component: Checkout },
        { path: '/checkout/success/:id', component: Success },
        { path: '/login', component: Login },
        { path: '/register', component: Register },
        { path: '/account', component: Account },
    ],
});
