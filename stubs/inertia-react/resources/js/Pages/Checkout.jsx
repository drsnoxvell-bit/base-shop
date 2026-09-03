import { router } from '@inertiajs/react';
import { useState } from 'react';

export default function Checkout({ lines = [], total_formatted, user }) {
    const [form, setForm] = useState({
        name: user?.name || '',
        phone: '',
        email: user?.email || '',
        address: '',
        comment: '',
    });

    const onChange = (key) => (e) => setForm({ ...form, [key]: e.target.value });

    return (
        <>
            <h1 className="shop-h1">Оформление заказа</h1>
            <div className="shop-checkout">
                <form className="shop-form" onSubmit={(e) => { e.preventDefault(); router.post('/checkout', form); }}>
                    <label>Имя <input value={form.name} onChange={onChange('name')} required /></label>
                    <label>Телефон <input value={form.phone} onChange={onChange('phone')} required /></label>
                    <label>Email <input type="email" value={form.email} onChange={onChange('email')} /></label>
                    <label>Адрес доставки <textarea rows="3" value={form.address} onChange={onChange('address')} required /></label>
                    <label>Комментарий <textarea rows="3" value={form.comment} onChange={onChange('comment')} /></label>
                    <button className="shop-btn" type="submit">Подтвердить заказ</button>
                </form>
                <aside className="shop-summary">
                    <h2>Ваш заказ</h2>
                    {lines.map((line) => (
                        <div key={line.id} className="shop-summary-row">
                            <span>{line.name} × {line.qty}</span>
                            <span>{line.sum_formatted}</span>
                        </div>
                    ))}
                    <div className="shop-summary-row shop-summary-total">
                        <span>Итого</span>
                        <strong>{total_formatted}</strong>
                    </div>
                </aside>
            </div>
        </>
    );
}
