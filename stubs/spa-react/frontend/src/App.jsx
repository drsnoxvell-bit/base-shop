import { createContext, useContext, useEffect, useState } from 'react';
import { Link, Route, Routes } from 'react-router-dom';
import { api } from './api';
import Home from './pages/Home';
import Catalog, {
    Category,
    Product,
    Cart,
    Checkout,
    Success,
    Login,
    Register,
    Account,
} from './pages/Catalog';

export const ShopContext = createContext({});

export function useShop() {
    return useContext(ShopContext);
}

export default function App() {
    const [state, setState] = useState({ shopSite: {}, cartCount: 0, navCategories: [], auth: { user: null } });

    const refresh = async () => {
        const { data } = await api.get('/api/shop/bootstrap');
        setState(data);
    };

    useEffect(() => { refresh(); }, []);

    const logout = async () => {
        await api.post('/api/logout');
        await refresh();
    };

    return (
        <ShopContext.Provider value={{ ...state, refresh }}>
            <div className="shop-body min-h-screen flex flex-col">
                <header className="shop-header">
                    <div className="shop-wrap flex items-center justify-between gap-4 py-4">
                        <Link to="/" className="shop-logo">{state.shopSite?.name || 'Магазин'}</Link>
                        <nav className="shop-nav hidden md:flex items-center gap-6">
                            <Link to="/">Главная</Link>
                            <Link to="/catalog">Каталог</Link>
                            {(state.navCategories || []).map((category) => (
                                <Link key={category.id} to={`/category/${category.slug}`}>{category.name}</Link>
                            ))}
                        </nav>
                        <Link to="/cart" className="shop-cart-link">
                            Корзина <span className="shop-cart-badge">{state.cartCount}</span>
                        </Link>
                        <div className="shop-auth-links">
                            {state.auth?.user ? (
                                <>
                                    <Link to="/account">{state.auth.user.name}</Link>
                                    <button type="button" className="shop-link-danger" onClick={logout}>Выйти</button>
                                </>
                            ) : (
                                <>
                                    <Link to="/login">Войти</Link>
                                    <Link to="/register">Регистрация</Link>
                                </>
                            )}
                        </div>
                    </div>
                </header>
                <main className="flex-1">
                    <div className="shop-wrap py-8">
                        <Routes>
                            <Route path="/" element={<Home />} />
                            <Route path="/catalog" element={<Catalog />} />
                            <Route path="/category/:slug" element={<Category />} />
                            <Route path="/product/:slug" element={<Product />} />
                            <Route path="/cart" element={<Cart />} />
                            <Route path="/checkout" element={<Checkout />} />
                            <Route path="/checkout/success/:id" element={<Success />} />
                            <Route path="/login" element={<Login />} />
                            <Route path="/register" element={<Register />} />
                            <Route path="/account" element={<Account />} />
                        </Routes>
                    </div>
                </main>
            </div>
        </ShopContext.Provider>
    );
}
