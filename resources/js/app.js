import './bootstrap';
import Swiper from 'swiper/bundle';
import GLightbox from 'glightbox';

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.product-swiper')) {
        new Swiper('.product-swiper', {
            loop: false,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }

    if (document.querySelector('.glightbox')) {
        GLightbox({ selector: '.glightbox' });
    }
});
