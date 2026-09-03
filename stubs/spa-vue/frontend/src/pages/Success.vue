<template>
    <div class="shop-success">
        <h1 class="shop-h1">Спасибо!</h1>
        <p v-if="order.number">Заказ <strong>{{ order.number }}</strong> принят.</p>
        <p>Сумма: {{ order.total_formatted }}</p>
        <RouterLink to="/" class="shop-btn">На главную</RouterLink>
    </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { api } from '../api';

const route = useRoute();
const order = reactive({});

onMounted(async () => {
    const { data } = await api.get(`/api/shop/orders/${route.params.id}`);
    Object.assign(order, data.order);
});
</script>
