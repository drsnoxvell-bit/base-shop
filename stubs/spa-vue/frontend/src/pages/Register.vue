<template>
    <h1 class="shop-h1">Регистрация</h1>
    <form class="shop-form" style="max-width: 28rem" @submit.prevent="submit">
        <label>Имя <input v-model="form.name" required></label>
        <label>Email <input v-model="form.email" type="email" required></label>
        <label>Пароль <input v-model="form.password" type="password" required></label>
        <label>Повтор пароля <input v-model="form.password_confirmation" type="password" required></label>
        <button class="shop-btn" type="submit">Создать аккаунт</button>
    </form>
    <div class="shop-social">
        <a class="shop-btn-ghost" href="/auth/yandex/redirect">Яндекс</a>
        <a class="shop-btn-ghost" href="/auth/vkontakte/redirect">ВКонтакте</a>
    </div>
</template>

<script setup>
import { inject, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '../api';

const router = useRouter();
const refreshShop = inject('refreshShop');
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

async function submit() {
    await api.post('/api/register', form);
    await refreshShop?.();
    await router.push('/');
}
</script>
