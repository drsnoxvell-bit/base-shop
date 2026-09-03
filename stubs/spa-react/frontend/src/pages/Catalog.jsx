import { useEffect, useState } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import { api } from '../api';
import { useShop } from '../App';

export function Catalog() {
    const [products, setProducts] = useState({ data: [] });
    useEffect(() => { api.get('/api/shop/catalog').then((r) => setProducts(r.data.products)); }, []);
    return (
        <>
            <h1 className="shop-h1">Каталог</h1>
            <div className="shop-grid-products">
                {(products.data || []).map((product) => (
                    <div key={product.id} className="shop-product-card">
                        <Link to={`/product/${product.slug}`} className="shop-product-name">{product.name}</Link>
                        <div className="shop-price-row"><strong>{product.price_formatted}</strong></div>
                    </div>
                ))}
            </div>
        </>
    );
}

export function Category() {
    const { slug } = useParams();
    const [data, setData] = useState({ category: {}, products: { data: [] } });
    useEffect(() => { api.get(`/api/shop/category/${slug}`).then((r) => setData(r.data)); }, [slug]);
    return (
        <>
            <h1 className="shop-h1">{data.category?.name}</h1>
            <div className="shop-grid-products">
                {(data.products?.data || []).map((product) => (
                    <Link key={product.id} to={`/product/${product.slug}`} className="shop-product-card">
                        <div className="shop-product-body">{product.name}</div>
                    </Link>
                ))}
            </div>
        </>
    );
}

export function Product() {
    const { slug } = useParams();
    const { refresh } = useShop();
    const [product, setProduct] = useState({});
    useEffect(() => { api.get(`/api/shop/product/${slug}`).then((r) => setProduct(r.data.product)); }, [slug]);
    return (
        <>
            <h1 className="shop-h1">{product.name}</h1>
            <p>{product.description}</p>
            <button className="shop-btn" type="button" disabled={!product.in_stock} onClick={async () => { await api.post(`/api/shop/cart/${product.id}`, { qty: 1 }); await refresh?.(); }}>
                В корзину
            </button>
        </>
    );
}

export function Cart() {
    const [cart, setCart] = useState({ empty: true, lines: [] });
    const { refresh } = useShop();
    const load = async () => {
        const { data } = await api.get('/api/shop/cart');
        setCart(data);
        await refresh?.();
    };
    useEffect(() => { load(); }, []);
    if (cart.empty) return <p>Корзина пуста. <Link to="/catalog">Каталог</Link></p>;
    return (
        <>
            <h1 className="shop-h1">Корзина</h1>
            {cart.lines.map((line) => (
                <div key={line.id} className="shop-summary-row">
                    <span>{line.name} × {line.qty}</span>
                    <span>{line.sum_formatted}</span>
                </div>
            ))}
            <Link to="/checkout" className="shop-btn">Оформить заказ</Link>
        </>
    );
}

export function Checkout() {
    const { auth, refresh } = useShop();
    const navigate = useNavigate();
    const [form, setForm] = useState({ name: auth?.user?.name || '', phone: '', email: auth?.user?.email || '', address: '', comment: '' });
    const onChange = (key) => (e) => setForm({ ...form, [key]: e.target.value });
    return (
        <form className="shop-form" onSubmit={async (e) => {
            e.preventDefault();
            const { data } = await api.post('/api/shop/checkout', form);
            await refresh?.();
            navigate(`/checkout/success/${data.order.id}`);
        }}>
            <h1 className="shop-h1">Оформление заказа</h1>
            <label>Имя <input value={form.name} onChange={onChange('name')} required /></label>
            <label>Телефон <input value={form.phone} onChange={onChange('phone')} required /></label>
            <label>Email <input type="email" value={form.email} onChange={onChange('email')} /></label>
            <label>Адрес <textarea value={form.address} onChange={onChange('address')} required /></label>
            <button className="shop-btn" type="submit">Подтвердить заказ</button>
        </form>
    );
}

export function Success() {
    const { id } = useParams();
    const [order, setOrder] = useState({});
    useEffect(() => { api.get(`/api/shop/orders/${id}`).then((r) => setOrder(r.data.order)); }, [id]);
    return (
        <div className="shop-success">
            <h1 className="shop-h1">Спасибо!</h1>
            <p>Заказ <strong>{order.number}</strong> принят.</p>
            <Link to="/" className="shop-btn">На главную</Link>
        </div>
    );
}

export function Login() {
    const { refresh } = useShop();
    const navigate = useNavigate();
    const [form, setForm] = useState({ email: '', password: '' });
    return (
        <form className="shop-form" onSubmit={async (e) => { e.preventDefault(); await api.post('/api/login', form); await refresh?.(); navigate('/'); }}>
            <h1 className="shop-h1">Вход</h1>
            <label>Email <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} required /></label>
            <label>Пароль <input type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} required /></label>
            <button className="shop-btn" type="submit">Войти</button>
            <a className="shop-btn-ghost" href="/auth/yandex/redirect">Яндекс</a>
            <a className="shop-btn-ghost" href="/auth/vkontakte/redirect">ВКонтакте</a>
        </form>
    );
}

export function Register() {
    const { refresh } = useShop();
    const navigate = useNavigate();
    const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });
    return (
        <form className="shop-form" onSubmit={async (e) => { e.preventDefault(); await api.post('/api/register', form); await refresh?.(); navigate('/'); }}>
            <h1 className="shop-h1">Регистрация</h1>
            <label>Имя <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required /></label>
            <label>Email <input type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} required /></label>
            <label>Пароль <input type="password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} required /></label>
            <label>Повтор <input type="password" value={form.password_confirmation} onChange={(e) => setForm({ ...form, password_confirmation: e.target.value })} required /></label>
            <button className="shop-btn" type="submit">Создать аккаунт</button>
        </form>
    );
}

export function Account() {
    const [form, setForm] = useState({ name: '', email: '' });
    const [orders, setOrders] = useState([]);
    useEffect(() => {
        api.get('/api/account').then((r) => {
            setForm(r.data.user);
            setOrders(r.data.orders || []);
        });
    }, []);
    return (
        <>
            <h1 className="shop-h1">Профиль</h1>
            <form className="shop-form" onSubmit={async (e) => { e.preventDefault(); await api.put('/api/account', form); }}>
                <label>Имя <input value={form.name || ''} onChange={(e) => setForm({ ...form, name: e.target.value })} /></label>
                <label>Email <input type="email" value={form.email || ''} onChange={(e) => setForm({ ...form, email: e.target.value })} /></label>
                <button className="shop-btn" type="submit">Сохранить</button>
            </form>
            <h2 className="shop-h2 mt-12">Мои заказы</h2>
            {orders.map((order) => <div key={order.id}>{order.number} · {order.total_formatted}</div>)}
        </>
    );
}

export default Catalog;
