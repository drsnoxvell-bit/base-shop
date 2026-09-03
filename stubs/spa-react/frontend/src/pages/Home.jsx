import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../api';
import ProductCard from '../components/ProductCard';

export default function Home() {
    const [data, setData] = useState({ site: {}, categories: [], products: [] });

    useEffect(() => {
        api.get('/api/shop/home').then((r) => setData(r.data));
    }, []);

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
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>
            </section>
        </>
    );
}
