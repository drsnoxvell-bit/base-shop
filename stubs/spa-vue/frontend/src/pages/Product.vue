<template>
    <div v-if="product.id">
        <h1 class="shop-h1">{{ product.name }}</h1>
        <div class="shop-price-row shop-price-lg"><strong>{{ product.price_formatted }}</strong></div>
        <p class="mt-4">{{ product.description }}</p>
        <button class="shop-btn mt-4" type="button" :disabled="!product.in_stock" @click="add">В корзину</button>
    </div>
</template>

<script setup>
import { inject, onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { api } from '../api';

const route = useRoute();
const product = reactive({});
const refreshShop = inject('refreshShop');

onMounted(async () => {
    const { data } = await api.get(`/api/shop/product/${route.params.slug}`);
    Object.assign(product, data.product);
});

async function add() {
    await api.post(`/api/shop/cart/${product.id}`, { qty: 1 });
    await refreshShop?.();
}
</script>
