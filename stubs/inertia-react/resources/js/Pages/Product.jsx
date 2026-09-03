import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import ProductCard from '../Components/ProductCard';

export default function Product({ product }) {
    const [qty, setQty] = useState(1);

    return (
        <>
            <p className="shop-crumb">
                <Link href="/catalog">Каталог</Link>
                {product.category && <> / <Link href={`/category/${product.category.slug}`}>{product.category.name}</Link></>}
                / {product.name}
            </p>
            <div className="shop-product-page">
                <div>
                    {product.gallery?.length ? product.gallery.map((image) => (
                        <a key={image.url} href={image.url}><img src={image.url} alt={product.name} /></a>
                    )) : <div className="shop-photo-empty shop-photo-lg">Нет фото</div>}
                </div>
                <div>
                    <h1 className="shop-h1">{product.name}</h1>
                    <div className="shop-price-row shop-price-lg">
                        <strong>{product.price_formatted}</strong>
                    </div>
                    <p className="mt-4">{product.description}</p>
                    <div className="shop-add-form">
                        <label>Количество
                            <input type="number" min="1" value={qty} onChange={(e) => setQty(Number(e.target.value))} />
                        </label>
                        <button className="shop-btn" type="button" disabled={!product.in_stock} onClick={() => router.post(`/cart/add/${product.id}`, { qty })}>
                            {product.in_stock ? 'Добавить в корзину' : 'Нет в наличии'}
                        </button>
                    </div>
                </div>
            </div>
            {product.related?.length > 0 && (
                <>
                    <h2 className="shop-h2 mt-12">Похожие товары</h2>
                    <div className="shop-grid-products">
                        {product.related.map((item) => <ProductCard key={item.id} product={item} />)}
                    </div>
                </>
            )}
        </>
    );
}
