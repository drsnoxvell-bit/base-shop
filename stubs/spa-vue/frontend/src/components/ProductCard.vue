<template>
    <div class="shop-product-card">
        <RouterLink :to="`/product/${product.slug}`" class="shop-product-photo">
            <img v-if="product.cover_url" :src="product.cover_url" :alt="product.name">
            <span v-else class="shop-photo-empty">Нет фото</span>
        </RouterLink>
        <div class="shop-product-body">
            <RouterLink :to="`/product/${product.slug}`" class="shop-product-name">{{ product.name }}</RouterLink>
            <div class="shop-price-row">
                <strong>{{ product.price_formatted }}</strong>
            </div>
            <button class="shop-btn shop-btn-sm mt-3" type="button" :disabled="!product.in_stock" @click="add">
                {{ product.in_stock ? 'В корзину' : 'Нет в наличии' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue';
import { api } from '../api';

const props = defineProps({ product: Object });
const refreshShop = inject('refreshShop');

async function add() {
    await api.post(`/api/shop/cart/${props.product.id}`, { qty: 1 });
    await refreshShop?.();
}
</script>
