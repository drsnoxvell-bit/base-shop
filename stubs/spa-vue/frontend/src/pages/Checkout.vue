<template>
    <h1 class="shop-h1">Оформление заказа</h1>
    <form class="shop-form" @submit.prevent="submit">
        <label>Имя <input v-model="form.name" required></label>
        <label>Телефон <input v-model="form.phone" required></label>
        <label>Email <input v-model="form.email" type="email"></label>
        <label>Адрес <textarea v-model="form.address" required></textarea></label>
        <label>Комментарий <textarea v-model="form.comment"></textarea></label>
        <button class="shop-btn" type="submit">Подтвердить заказ</button>
    </form>
</template>

<script setup>
import { inject, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '../api';

const shop = inject('shop');
const router = useRouter();
const form = reactive({
    name: shop?.auth?.user?.name || '',
    phone: '',
    email: shop?.auth?.user?.email || '',
    address: '',
    comment: '',
});

async function submit() {
    const { data } = await api.post('/api/shop/checkout', form);
    await router.push(`/checkout/success/${data.order.id}`);
}
</script>
