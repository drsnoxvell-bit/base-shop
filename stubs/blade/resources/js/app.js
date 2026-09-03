import './bootstrap';
import Swiper from 'swiper/bundle';
import GLightbox from 'glightbox';

document.addEventListener('DOMContentLoaded', () => {
    const swiperEl = document.querySelector('.product-swiper');
    if (swiperEl) {
        const multiple = swiperEl.querySelectorAll('.swiper-slide').length > 1;
        new Swiper(swiperEl, {
            loop: false,
            pagination: multiple ? { el: '.swiper-pagination', clickable: true } : undefined,
            navigation: multiple
                ? {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                }
                : undefined,
        });
    }

    if (document.querySelector('.glightbox')) {
        GLightbox({ selector: '.glightbox' });
    }

    initCartQty();
});

function initCartQty() {
    document.querySelectorAll('.shop-qty').forEach((el) => {
        el.addEventListener('click', async (event) => {
            const btn = event.target.closest('.shop-qty-btn');
            if (!btn || el.classList.contains('is-busy')) {
                return;
            }

            const valueEl = el.querySelector('.shop-qty-value');
            const max = Number(el.dataset.max);
            const next = Number(valueEl.textContent) + Number(btn.dataset.dir);

            if (Number.isNaN(next) || next < 0 || next > max) {
                return;
            }

            el.classList.add('is-busy');

            try {
                const { data } = await window.axios.patch(el.dataset.url, { qty: next });
                applyCartState(data);
            } catch (error) {
                const message = error.response?.data?.message
                    || error.response?.data?.errors?.qty?.[0]
                    || 'Не удалось обновить корзину';
                window.alert(message);
            } finally {
                el.classList.remove('is-busy');
            }
        });
    });
}

function applyCartState(data) {
    if (data.empty) {
        window.location.reload();
        return;
    }

    const badge = document.querySelector('.shop-cart-badge');
    if (badge) {
        badge.textContent = data.count;
    }

    const total = document.querySelector('[data-cart-total]');
    if (total) {
        total.textContent = data.total_formatted;
    }

    data.lines.forEach((line) => {
        const row = document.querySelector(`[data-line="${line.id}"]`);
        if (!row) {
            return;
        }

        const qty = row.querySelector('.shop-qty-value');
        const sum = row.querySelector('.shop-line-sum');
        if (qty) {
            qty.textContent = line.qty;
        }
        if (sum) {
            sum.textContent = line.sum_formatted;
        }
    });
}
