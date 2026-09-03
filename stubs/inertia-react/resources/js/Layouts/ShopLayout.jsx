import { Link, router, usePage } from '@inertiajs/react';

export default function ShopLayout({ children }) {
    const { shopSite, cartCount, navCategories = [], auth, flash } = usePage().props;

    return (
        <div className="shop-body min-h-screen flex flex-col">
            <header className="shop-header">
                <div className="shop-wrap flex items-center justify-between gap-4 py-4">
                    <Link href="/" className="shop-logo">{shopSite?.name || 'Магазин'}</Link>
                    <nav className="shop-nav hidden md:flex items-center gap-6">
                        <Link href="/">Главная</Link>
                        <Link href="/catalog">Каталог</Link>
                        {navCategories.map((category) => (
                            <Link key={category.id} href={`/category/${category.slug}`}>{category.name}</Link>
                        ))}
                    </nav>
                    <Link href="/cart" className="shop-cart-link">
                        Корзина
                        <span className="shop-cart-badge">{cartCount}</span>
                    </Link>
                    <div className="shop-auth-links">
                        {auth?.user ? (
                            <>
                                <Link href="/account">{auth.user.name}</Link>
                                <button type="button" className="shop-link-danger" onClick={() => router.post('/logout')}>Выйти</button>
                            </>
                        ) : (
                            <>
                                <Link href="/login">Войти</Link>
                                <Link href="/register">Регистрация</Link>
                            </>
                        )}
                    </div>
                </div>
            </header>
            <main className="flex-1">
                <div className="shop-wrap py-8">
                    {flash?.success && <div className="shop-alert shop-alert-ok">{flash.success}</div>}
                    {flash?.error && <div className="shop-alert shop-alert-err">{flash.error}</div>}
                    {children}
                </div>
            </main>
            <footer className="shop-footer">
                <div className="shop-wrap py-8">
                    <div className="shop-logo">{shopSite?.name}</div>
                    <p className="mt-2 text-sm opacity-80">{shopSite?.description}</p>
                </div>
            </footer>
        </div>
    );
}
