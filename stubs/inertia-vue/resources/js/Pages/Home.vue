<template>
    <section class="shop-hero">
        <p class="shop-kicker">Интернет-магазин</p>
        <h1>{{ site?.name || 'Магазин' }}</h1>
        <p class="shop-lead">{{ site?.description }}</p>
        <Link href="/catalog" class="shop-btn">Смотреть каталог</Link>
    </section>
    <section class="mt-12">
        <h2 class="shop-h2">Категории</h2>
        <div class="shop-grid-cats">
            <Link v-for="category in categories" :key="category.id" class="shop-cat-card" :href="`/category/${category.slug}`">
                <strong>{{ category.name }}</strong>
                <span>{{ category.products_count }} товаров</span>
            </Link>
        </div>
    </section>
    <section class="mt-12">
        <h2 class="shop-h2">Популярные товары</h2>
        <div class="shop-grid-products">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';

defineProps({ site: Object, categories: Array, products: Array });
</script>
