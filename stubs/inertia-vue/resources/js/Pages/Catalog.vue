<template>
    <h1 class="shop-h1">Каталог</h1>
    <div class="shop-grid-products">
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
        <p v-if="!products.data?.length">Товаров пока нет.</p>
    </div>
    <div class="mt-8 flex flex-wrap gap-2">
        <Link v-for="link in products.links" :key="link.label" :href="link.url || '#'" class="shop-btn-ghost" v-html="link.label" />
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';

defineProps({ products: Object });
</script>
