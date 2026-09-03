<template>
    <h1 class="shop-h1">Оформление заказа</h1>
    <p class="shop-muted">Оплата при получении. После отправки заявки с вами свяжется менеджер.</p>
    <div class="shop-checkout">
        <form class="shop-form" @submit.prevent="submit">
            <label>Имя <input v-model="form.name" required></label>
            <label>Телефон <input v-model="form.phone" required></label>
            <label>Email <input v-model="form.email" type="email"></label>
            <label>Адрес доставки <textarea v-model="form.address" rows="3" required></textarea></label>
            <label>Комментарий <textarea v-model="form.comment" rows="3"></textarea></label>
            <button class="shop-btn" type="submit">Подтвердить заказ</button>
        </form>
        <aside class="shop-summary">
            <h2>Ваш заказ</h2>
            <div v-for="line in lines" :key="line.id" class="shop-summary-row">
                <span>{{ line.name }} × {{ line.qty }}</span>
                <span>{{ line.sum_formatted }}</span>
            </div>
            <div class="shop-summary-row shop-summary-total">
                <span>Итого</span>
                <strong>{{ total_formatted }}</strong>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ lines: Array, total_formatted: String, user: Object });
const form = reactive({
    name: props.user?.name || '',
    phone: '',
    email: props.user?.email || '',
    address: '',
    comment: '',
});

function submit() {
    router.post('/checkout', form);
}
</script>
