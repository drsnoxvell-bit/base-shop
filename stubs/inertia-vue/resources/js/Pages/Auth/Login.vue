<template>
    <h1 class="shop-h1">Вход</h1>
    <form class="shop-form" style="max-width: 28rem" @submit.prevent="submit">
        <label>Email <input v-model="form.email" type="email" required></label>
        <label>Пароль <input v-model="form.password" type="password" required></label>
        <label class="shop-check"><input v-model="form.remember" type="checkbox" value="1"> Запомнить меня</label>
        <button class="shop-btn" type="submit">Войти</button>
    </form>
    <div class="shop-social">
        <p class="shop-muted">Или войти через</p>
        <div class="shop-social-row">
            <a class="shop-btn-ghost" href="/auth/yandex/redirect">Яндекс</a>
            <a class="shop-btn-ghost" href="/auth/vkontakte/redirect">ВКонтакте</a>
        </div>
    </div>
    <p class="mt-4">Нет аккаунта? <Link href="/register">Регистрация</Link></p>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const form = reactive({ email: '', password: '', remember: false });

function submit() {
    router.post('/login', form);
}
</script>
