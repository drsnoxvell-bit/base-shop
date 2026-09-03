<template>
    <h1 class="shop-h1">Каталог</h1>
    <div class="shop-grid-products">
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
        <p v-if="products.data && !products.data.length">Товаров пока нет.</p>
    </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { api } from '../api';
import ProductCard from '../components/ProductCard.vue';

const products = reactive({ data: [] });

onMounted(async () => {
    const { data } = await api.get('/api/shop/catalog');
    Object.assign(products, data.products);
});
</script>
