import { Link } from '@inertiajs/react';
import ProductCard from '../Components/ProductCard';

export default function Home({ site, categories = [], products = [] }) {
    return (
        <>
            <section className="shop-hero">
                <p className="shop-kicker">Интернет-магазин</p>
                <h1>{site?.name || 'Магазин'}</h1>
                <p className="shop-lead">{site?.description}</p>
                <Link href="/catalog" className="shop-btn">Смотреть каталог</Link>
            </section>
            <section className="mt-12">
                <h2 className="shop-h2">Категории</h2>
                <div className="shop-grid-cats">
                    {categories.map((category) => (
                        <Link key={category.id} className="shop-cat-card" href={`/category/${category.slug}`}>
                            <strong>{category.name}</strong>
                            <span>{category.products_count} товаров</span>
                        </Link>
                    ))}
                </div>
            </section>
            <section className="mt-12">
                <h2 className="shop-h2">Популярные товары</h2>
                <div className="shop-grid-products">
                    {products.map((product) => <ProductCard key={product.id} product={product} />)}
                </div>
            </section>
        </>
    );
}
