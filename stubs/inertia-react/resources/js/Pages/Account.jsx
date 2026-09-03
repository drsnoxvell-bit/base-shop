import { router } from '@inertiajs/react';
import { useState } from 'react';

export default function Account({ user, orders = [] }) {
    const [form, setForm] = useState({ name: user?.name || '', email: user?.email || '' });

    return (
        <>
            <h1 className="shop-h1">Профиль</h1>
            <form className="shop-form" style={{ maxWidth: '28rem' }} onSubmit={(e) => { e.preventDefault(); router.put('/account', form); }}>
                <label>Имя <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required /></label>
                <label>Email <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} required /></label>
                <button className="shop-btn" type="submit">Сохранить</button>
            </form>
            <h2 className="shop-h2 mt-12">Мои заказы</h2>
            {orders.map((order) => (
                <div key={order.id} className="shop-summary mb-4">
                    <div className="shop-summary-row">
                        <strong>{order.number}</strong>
                        <span>{order.total_formatted}</span>
                    </div>
                </div>
            ))}
            {!orders.length && <p>Заказов пока нет.</p>}
        </>
    );
}
