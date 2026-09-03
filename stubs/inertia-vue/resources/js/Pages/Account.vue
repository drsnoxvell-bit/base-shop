<template>
    <h1 class="shop-h1">Профиль</h1>
    <form class="shop-form" style="max-width: 28rem" @submit.prevent="save">
        <label>Имя <input v-model="form.name" required></label>
        <label>Email <input v-model="form.email" type="email" required></label>
        <button class="shop-btn" type="submit">Сохранить</button>
    </form>
    <h2 class="shop-h2 mt-12">Мои заказы</h2>
    <div v-for="order in orders" :key="order.id" class="shop-summary mb-4">
        <div class="shop-summary-row">
            <strong>{{ order.number }}</strong>
            <span>{{ order.total_formatted }}</span>
        </div>
    </div>
    <p v-if="!orders?.length">Заказов пока нет.</p>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ user: Object, orders: Array });
const form = reactive({ name: props.user?.name || '', email: props.user?.email || '' });

function save() {
    router.put('/account', form);
}
</script>
