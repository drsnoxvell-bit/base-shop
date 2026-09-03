<template>
    <h1 class="shop-h1">Вход</h1>
    <form class="shop-form" style="max-width: 28rem" @submit.prevent="submit">
        <label>Email <input v-model="form.email" type="email" required></label>
        <label>Пароль <input v-model="form.password" type="password" required></label>
        <button class="shop-btn" type="submit">Войти</button>
    </form>
    <div class="shop-social">
        <a class="shop-btn-ghost" href="/auth/yandex/redirect">Яндекс</a>
        <a class="shop-btn-ghost" href="/auth/vkontakte/redirect">ВКонтакте</a>
    </div>
    <p class="mt-4"><RouterLink to="/register">Регистрация</RouterLink></p>
</template>

<script setup>
import { inject, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '../api';

const router = useRouter();
const refreshShop = inject('refreshShop');
const form = reactive({ email: '', password: '' });

async function submit() {
    await api.post('/api/login', form);
    await refreshShop?.();
    await router.push('/');
}
</script>
