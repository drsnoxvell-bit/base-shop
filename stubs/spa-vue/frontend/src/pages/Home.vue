<template>
    <section class="shop-hero">
        <h1>{{ data.site?.name || 'Магазин' }}</h1>
        <p class="shop-lead">{{ data.site?.description }}</p>
        <RouterLink to="/catalog" class="shop-btn">Смотреть каталог</RouterLink>
    </section>
    <section class="mt-12">
        <h2 class="shop-h2">Категории</h2>
        <div class="shop-grid-cats">
            <RouterLink v-for="category in data.categories" :key="category.id" class="shop-cat-card" :to="`/category/${category.slug}`">
                <strong>{{ category.name }}</strong>
                <span>{{ category.products_count }} товаров</span>
            </RouterLink>
        </div>
    </section>
    <section class="mt-12">
        <h2 class="shop-h2">Популярные товары</h2>
        <div class="shop-grid-products">
            <ProductCard v-for="product in data.products" :key="product.id" :product="product" />
        </div>
    </section>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { api } from '../api';
import ProductCard from '../components/ProductCard.vue';

const data = reactive({ site: {}, categories: [], products: [] });

onMounted(async () => {
    const { data: payload } = await api.get('/api/shop/home');
    Object.assign(data, payload);
});
</script>
