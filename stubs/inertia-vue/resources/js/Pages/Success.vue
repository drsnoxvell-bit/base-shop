<template>
    <div class="shop-success">
        <h1 class="shop-h1">Спасибо!</h1>
        <p>Заказ <strong>{{ order.number }}</strong> принят. Мы свяжемся с вами для подтверждения.</p>
        <p>Сумма: {{ order.total_formatted }}</p>
        <Link href="/" class="shop-btn">На главную</Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ order: Object });
</script>
