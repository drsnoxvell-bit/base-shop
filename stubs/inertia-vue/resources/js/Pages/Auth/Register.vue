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
        <p class="shop-muted">Или войти через</p>
        <div class="shop-social-row">
            <a class="shop-btn-ghost" href="/auth/yandex/redirect">Яндекс</a>
            <a class="shop-btn-ghost" href="/auth/vkontakte/redirect">ВКонтакте</a>
        </div>
    </div>
    <p class="mt-4">Уже есть аккаунт? <Link href="/login">Войти</Link></p>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

function submit() {
    router.post('/register', form);
}
</script>
