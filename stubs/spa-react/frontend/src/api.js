import axios from 'axios';

const csrf = document.head.querySelector('meta[name="csrf-token"]')?.content;

export const api = axios.create({
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
    },
});
