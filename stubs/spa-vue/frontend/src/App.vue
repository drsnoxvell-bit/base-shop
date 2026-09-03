<template>
    <div class="shop-body min-h-screen flex flex-col">
        <header class="shop-header">
            <div class="shop-wrap flex items-center justify-between gap-4 py-4">
                <RouterLink to="/" class="shop-logo">{{ state.shopSite?.name || 'Магазин' }}</RouterLink>
                <nav class="shop-nav hidden md:flex items-center gap-6">
                    <RouterLink to="/">Главная</RouterLink>
                    <RouterLink to="/catalog">Каталог</RouterLink>
                    <RouterLink v-for="category in state.navCategories" :key="category.id" :to="`/category/${category.slug}`">
                        {{ category.name }}
                    </RouterLink>
                </nav>
                <RouterLink to="/cart" class="shop-cart-link">
                    Корзина
                    <span class="shop-cart-badge">{{ state.cartCount }}</span>
                </RouterLink>
                <div class="shop-auth-links">
                    <template v-if="state.auth?.user">
                        <RouterLink to="/account">{{ state.auth.user.name }}</RouterLink>
                        <button type="button" class="shop-link-danger" @click="logout">Выйти</button>
                    </template>
                    <template v-else>
                        <RouterLink to="/login">Войти</RouterLink>
                        <RouterLink to="/register">Регистрация</RouterLink>
                    </template>
                </div>
            </div>
        </header>
        <main class="flex-1">
            <div class="shop-wrap py-8">
                <RouterView />
            </div>
        </main>
    </div>
</template>

<script setup>
import { onMounted, reactive, provide } from 'vue';
import { api, bootstrap } from './api';

const state = reactive({
    shopSite: {},
    cartCount: 0,
    navCategories: [],
    auth: { user: null },
});

provide('shop', state);
provide('refreshShop', refresh);

async function refresh() {
    Object.assign(state, await bootstrap());
}

async function logout() {
    await api.post('/api/logout');
    await refresh();
}

onMounted(refresh);
</script>
