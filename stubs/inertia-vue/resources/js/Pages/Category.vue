<template>
    <p class="shop-crumb"><Link href="/catalog">Каталог</Link> / {{ category.name }}</p>
    <h1 class="shop-h1">{{ category.name }}</h1>
    <p v-if="category.description" class="shop-lead">{{ category.description }}</p>
    <div class="shop-grid-products">
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
        <p v-if="!products.data?.length">В этой категории пока нет товаров.</p>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';

defineProps({ category: Object, products: Object });
</script>
