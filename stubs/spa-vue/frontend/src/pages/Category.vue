<template>
    <p class="shop-crumb"><RouterLink to="/catalog">Каталог</RouterLink> / {{ category.name }}</p>
    <h1 class="shop-h1">{{ category.name }}</h1>
    <div class="shop-grid-products">
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
    </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { api } from '../api';
import ProductCard from '../components/ProductCard.vue';

const route = useRoute();
const category = reactive({ name: '' });
const products = reactive({ data: [] });

onMounted(async () => {
    const { data } = await api.get(`/api/shop/category/${route.params.slug}`);
    Object.assign(category, data.category);
    Object.assign(products, data.products);
});
</script>
