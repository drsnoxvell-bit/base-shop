<template>
    <h1 class="shop-h1">Корзина</h1>
    <p v-if="empty">Корзина пуста. <Link href="/catalog">Перейти в каталог</Link></p>
    <template v-else>
        <div class="shop-table-wrap">
            <table class="shop-table">
                <thead>
                    <tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th><th></th></tr>
                </thead>
                <tbody>
                    <tr v-for="line in lines" :key="line.id">
                        <td><Link :href="`/product/${line.slug}`">{{ line.name }}</Link></td>
                        <td>{{ line.price_formatted }}</td>
                        <td>
                            <div class="shop-qty">
                                <button type="button" class="shop-qty-btn" @click="setQty(line, line.qty - 1)">−</button>
                                <span class="shop-qty-value">{{ line.qty }}</span>
                                <button type="button" class="shop-qty-btn" @click="setQty(line, line.qty + 1)">+</button>
                            </div>
                        </td>
                        <td>{{ line.sum_formatted }}</td>
                        <td><button type="button" class="shop-link-danger" @click="setQty(line, 0)">Удалить</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="shop-cart-total">
            <div>Итого: <strong>{{ total_formatted }}</strong></div>
            <Link href="/checkout" class="shop-btn">Оформить заказ</Link>
        </div>
    </template>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({ empty: Boolean, lines: Array, total_formatted: String });

function setQty(line, qty) {
    if (qty > line.max) return;
    router.patch(`/cart/${line.id}`, { qty });
}
</script>
