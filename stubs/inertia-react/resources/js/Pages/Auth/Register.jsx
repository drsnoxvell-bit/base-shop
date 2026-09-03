import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Register() {
    const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });

    return (
        <>
            <h1 className="shop-h1">Регистрация</h1>
            <form className="shop-form" style={{ maxWidth: '28rem' }} onSubmit={(e) => { e.preventDefault(); router.post('/register', form); }}>
                <label>Имя <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required /></label>
                <label>Email <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} required /></label>
                <label>Пароль <input type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} required /></label>
                <label>Повтор пароля <input type="password" value={form.password_confirmation} onChange={(e) => setForm({ ...form, password_confirmation: e.target.value })} required /></label>
                <button className="shop-btn" type="submit">Создать аккаунт</button>
            </form>
            <div className="shop-social">
                <p className="shop-muted">Или войти через</p>
                <div className="shop-social-row">
                    <a className="shop-btn-ghost" href="/auth/yandex/redirect">Яндекс</a>
                    <a className="shop-btn-ghost" href="/auth/vkontakte/redirect">ВКонтакте</a>
                </div>
            </div>
            <p className="mt-4">Уже есть аккаунт? <Link href="/login">Войти</Link></p>
        </>
    );
}
