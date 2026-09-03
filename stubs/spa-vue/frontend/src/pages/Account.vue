<template>
    <h1 class="shop-h1">Профиль</h1>
    <form class="shop-form" style="max-width: 28rem" @submit.prevent="save">
        <label>Имя <input v-model="form.name" required></label>
        <label>Email <input v-model="form.email" type="email" required></label>
        <button class="shop-btn" type="submit">Сохранить</button>
    </form>
    <h2 class="shop-h2 mt-12">Мои заказы</h2>
    <div v-for="order in orders" :key="order.id" class="shop-summary mb-4">
        <strong>{{ order.number }}</strong> · {{ order.total_formatted }}
    </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { api } from '../api';

const form = reactive({ name: '', email: '' });
const orders = reactive([]);

onMounted(async () => {
    const { data } = await api.get('/api/account');
    Object.assign(form, data.user);
    orders.splice(0, orders.length, ...(data.orders || []));
});

async function save() {
    await api.put('/api/account', form);
}
</script>
