<template>
    <div class="shop-body min-h-screen flex flex-col">
        <header class="shop-header">
            <div class="shop-wrap flex items-center justify-between gap-4 py-4">
                <Link href="/" class="shop-logo">{{ shopSite?.name || 'Магазин' }}</Link>
                <nav class="shop-nav hidden md:flex items-center gap-6">
                    <Link href="/">Главная</Link>
                    <Link href="/catalog">Каталог</Link>
                    <Link v-for="category in navCategories" :key="category.id" :href="`/category/${category.slug}`">
                        {{ category.name }}
                    </Link>
                </nav>
                <Link href="/cart" class="shop-cart-link">
                    Корзина
                    <span class="shop-cart-badge">{{ cartCount }}</span>
                </Link>
                <div class="shop-auth-links">
                    <template v-if="auth?.user">
                        <Link href="/account">{{ auth.user.name }}</Link>
                        <button type="button" class="shop-link-danger" @click="logout">Выйти</button>
                    </template>
                    <template v-else>
                        <Link href="/login">Войти</Link>
                        <Link href="/register">Регистрация</Link>
                    </template>
                </div>
            </div>
        </header>
        <main class="flex-1">
            <div class="shop-wrap py-8">
                <div v-if="flash?.success" class="shop-alert shop-alert-ok">{{ flash.success }}</div>
                <div v-if="flash?.error" class="shop-alert shop-alert-err">{{ flash.error }}</div>
                <slot />
            </div>
        </main>
        <footer class="shop-footer">
            <div class="shop-wrap py-8">
                <div class="shop-logo">{{ shopSite?.name }}</div>
                <p class="mt-2 text-sm opacity-80">{{ shopSite?.description }}</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const shopSite = computed(() => page.props.shopSite);
const cartCount = computed(() => page.props.cartCount);
const navCategories = computed(() => page.props.navCategories || []);
const auth = computed(() => page.props.auth);
const flash = computed(() => page.props.flash);

function logout() {
    router.post('/logout');
}
</script>
