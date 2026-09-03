<template>
    <p class="shop-crumb">
        <Link href="/catalog">Каталог</Link>
        <template v-if="product.category"> / <Link :href="`/category/${product.category.slug}`">{{ product.category.name }}</Link></template>
        / {{ product.name }}
    </p>
    <div class="shop-product-page">
        <div>
            <div v-if="product.gallery?.length" class="product-swiper">
                <a v-for="image in product.gallery" :key="image.url" :href="image.url">
                    <img :src="image.url" :alt="product.name">
                </a>
            </div>
            <div v-else class="shop-photo-empty shop-photo-lg">Нет фото</div>
        </div>
        <div>
            <h1 class="shop-h1">{{ product.name }}</h1>
            <p v-if="product.sku" class="shop-muted">Артикул: {{ product.sku }}</p>
            <div class="shop-price-row shop-price-lg">
                <strong>{{ product.price_formatted }}</strong>
                <s v-if="product.old_price_formatted">{{ product.old_price_formatted }}</s>
            </div>
            <p class="mt-4">{{ product.description }}</p>
            <p class="mt-3 shop-muted">На складе: {{ product.quantity }} шт.</p>
            <div class="shop-add-form">
                <label>Количество
                    <input v-model.number="qty" type="number" min="1" :max="product.quantity || 1">
                </label>
                <button class="shop-btn" type="button" :disabled="!product.in_stock" @click="add">
                    {{ product.in_stock ? 'Добавить в корзину' : 'Нет в наличии' }}
                </button>
            </div>
        </div>
    </div>
    <h2 v-if="product.related?.length" class="shop-h2 mt-12">Похожие товары</h2>
    <div v-if="product.related?.length" class="shop-grid-products">
        <ProductCard v-for="item in product.related" :key="item.id" :product="item" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';

const props = defineProps({ product: Object });
const qty = ref(1);

function add() {
    router.post(`/cart/add/${props.product.id}`, { qty: qty.value });
}
</script>
