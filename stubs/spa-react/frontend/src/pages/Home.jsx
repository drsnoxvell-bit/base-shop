import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../api';
import { useShop } from '../App';

export default function Home() {
    const [data, setData] = useState({ site: {}, categories: [], products: [] });
    const { refresh } = useShop();

    useEffect(() => {
        api.get('/api/shop/home').then((r) => setData(r.data));
    }, []);

    const add = async (id) => {
        await api.post(`/api/shop/cart/${id}`, { qty: 1 });
        await refresh?.();
    };

    return (
        <>
            <section className="shop-hero">
                <h1>{data.site?.name || 'Магазин'}</h1>
                <p className="shop-lead">{data.site?.description}</p>
                <Link to="/catalog" className="shop-btn">Смотреть каталог</Link>
            </section>
            <section className="mt-12">
                <h2 className="shop-h2">Категории</h2>
                <div className="shop-grid-cats">
                    {(data.categories || []).map((category) => (
                        <Link key={category.id} className="shop-cat-card" to={`/category/${category.slug}`}>
                            <strong>{category.name}</strong>
                            <span>{category.products_count} товаров</span>
                        </Link>
                    ))}
                </div>
            </section>
            <section className="mt-12">
                <h2 className="shop-h2">Популярные товары</h2>
                <div className="shop-grid-products">
                    {(data.products || []).map((product) => (
                        <div key={product.id} className="shop-product-card">
                            <Link to={`/product/${product.slug}`} className="shop-product-photo">
                                {product.cover_url ? <img src={product.cover_url} alt={product.name} /> : <span className="shop-photo-empty">Нет фото</span>}
                            </Link>
                            <div className="shop-product-body">
                                <Link to={`/product/${product.slug}`} className="shop-product-name">{product.name}</Link>
                                <div className="shop-price-row"><strong>{product.price_formatted}</strong></div>
                                <button className="shop-btn shop-btn-sm mt-3" type="button" disabled={!product.in_stock} onClick={() => add(product.id)}>
                                    {product.in_stock ? 'В корзину' : 'Нет в наличии'}
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </section>
        </>
    );
}
