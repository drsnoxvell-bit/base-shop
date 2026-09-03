<template>
    <article class="shop-product-card">
        <Link :href="`/product/${product.slug}`" class="shop-product-media">
            <img v-if="product.cover_url" :src="product.cover_url" :alt="product.name">
            <span v-else class="shop-photo-empty">Нет фото</span>
            <div class="shop-product-badges">
                <span v-if="product.discount_percent" class="shop-badge shop-badge-sale">−{{ product.discount_percent }}%</span>
                <span class="shop-badge shop-badge-stock" :class="'is-' + product.stock_status">{{ product.stock_label }}</span>
                <span v-if="product.photos_count > 1" class="shop-badge shop-badge-photos">{{ product.photos_count }} фото</span>
            </div>
        </Link>
        <div class="shop-product-body">
            <Link v-if="product.category" class="shop-product-cat" :href="`/category/${product.category.slug}`">{{ product.category.name }}</Link>
            <Link :href="`/product/${product.slug}`" class="shop-product-name">{{ product.name }}</Link>
            <p v-if="product.excerpt" class="shop-product-excerpt">{{ product.excerpt }}</p>
            <dl class="shop-product-meta">
                <div v-if="product.sku">
                    <dt>Артикул</dt>
                    <dd>{{ product.sku }}</dd>
                </div>
                <div>
                    <dt>Склад</dt>
                    <dd>{{ product.quantity }} шт.</dd>
                </div>
            </dl>
            <div class="shop-product-price">
                <span class="shop-product-price-now">{{ product.price_formatted }}</span>
                <s v-if="product.old_price_formatted">{{ product.old_price_formatted }}</s>
                <span v-if="product.savings_formatted" class="shop-product-save">Выгода {{ product.savings_formatted }}</span>
            </div>
            <div class="shop-product-actions">
                <Link class="shop-btn-ghost shop-btn-sm" :href="`/product/${product.slug}`">Подробнее</Link>
                <button class="shop-btn shop-btn-sm" type="button" :disabled="!product.in_stock" @click="add">
                    {{ product.in_stock ? 'В корзину' : 'Нет в наличии' }}
                </button>
            </div>
        </div>
    </article>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({ product: { type: Object, required: true } });

function add() {
    router.post(`/cart/add/${props.product.id}`, { qty: 1 });
}
</script>
