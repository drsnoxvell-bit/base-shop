<template>
    <div class="shop-product-card">
        <Link :href="`/product/${product.slug}`" class="shop-product-photo">
            <img v-if="product.cover_url" :src="product.cover_url" :alt="product.name">
            <span v-else class="shop-photo-empty">Нет фото</span>
        </Link>
        <div class="shop-product-body">
            <Link :href="`/product/${product.slug}`" class="shop-product-name">{{ product.name }}</Link>
            <div v-if="product.category" class="shop-muted">{{ product.category.name }}</div>
            <div class="shop-price-row">
                <strong>{{ product.price_formatted }}</strong>
                <s v-if="product.old_price_formatted">{{ product.old_price_formatted }}</s>
            </div>
            <button class="shop-btn shop-btn-sm mt-3" type="button" :disabled="!product.in_stock" @click="add">
                {{ product.in_stock ? 'В корзину' : 'Нет в наличии' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({ product: { type: Object, required: true } });

function add() {
    router.post(`/cart/add/${props.product.id}`, { qty: 1 });
}
</script>
