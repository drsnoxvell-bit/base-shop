<template>
    <h1 class="shop-h1">Корзина</h1>
    <p v-if="cart.empty">Корзина пуста. <RouterLink to="/catalog">Каталог</RouterLink></p>
    <template v-else>
        <div class="shop-table-wrap">
            <table class="shop-table">
                <tbody>
                    <tr v-for="line in cart.lines" :key="line.id">
                        <td><RouterLink :to="`/product/${line.slug}`">{{ line.name }}</RouterLink></td>
                        <td>
                            <div class="shop-qty">
                                <button type="button" class="shop-qty-btn" @click="setQty(line, line.qty - 1)">−</button>
                                <span class="shop-qty-value">{{ line.qty }}</span>
                                <button type="button" class="shop-qty-btn" @click="setQty(line, line.qty + 1)">+</button>
                            </div>
                        </td>
                        <td>{{ line.sum_formatted }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="shop-cart-total">
            <div>Итого: <strong>{{ cart.total_formatted }}</strong></div>
            <RouterLink to="/checkout" class="shop-btn">Оформить заказ</RouterLink>
        </div>
    </template>
</template>

<script setup>
import { inject, onMounted, reactive } from 'vue';
import { api } from '../api';

const cart = reactive({ empty: true, lines: [], total_formatted: '' });
const refreshShop = inject('refreshShop');

async function load() {
    const { data } = await api.get('/api/shop/cart');
    Object.assign(cart, data);
    await refreshShop?.();
}

async function setQty(line, qty) {
    await api.patch(`/api/shop/cart/${line.id}`, { qty });
    await load();
}

onMounted(load);
</script>
