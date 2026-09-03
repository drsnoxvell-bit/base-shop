import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Login() {
    const [form, setForm] = useState({ email: '', password: '', remember: false });

    return (
        <>
            <h1 className="shop-h1">Вход</h1>
            <form className="shop-form" style={{ maxWidth: '28rem' }} onSubmit={(e) => { e.preventDefault(); router.post('/login', form); }}>
                <label>Email <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} required /></label>
                <label>Пароль <input type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} required /></label>
                <button className="shop-btn" type="submit">Войти</button>
            </form>
            <div className="shop-social">
                <p className="shop-muted">Или войти через</p>
                <div className="shop-social-row">
                    <a className="shop-btn-ghost" href="/auth/yandex/redirect">Яндекс</a>
                    <a className="shop-btn-ghost" href="/auth/vkontakte/redirect">ВКонтакте</a>
                </div>
            </div>
            <p className="mt-4">Нет аккаунта? <Link href="/register">Регистрация</Link></p>
        </>
    );
}
