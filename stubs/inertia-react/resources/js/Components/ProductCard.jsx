import { Link, router } from '@inertiajs/react';

export default function ProductCard({ product }) {
    return (
        <div className="shop-product-card">
            <Link href={`/product/${product.slug}`} className="shop-product-photo">
                {product.cover_url ? <img src={product.cover_url} alt={product.name} /> : <span className="shop-photo-empty">Нет фото</span>}
            </Link>
            <div className="shop-product-body">
                <Link href={`/product/${product.slug}`} className="shop-product-name">{product.name}</Link>
                {product.category && <div className="shop-muted">{product.category.name}</div>}
                <div className="shop-price-row">
                    <strong>{product.price_formatted}</strong>
                    {product.old_price_formatted && <s>{product.old_price_formatted}</s>}
                </div>
                <button className="shop-btn shop-btn-sm mt-3" type="button" disabled={!product.in_stock} onClick={() => router.post(`/cart/add/${product.id}`, { qty: 1 })}>
                    {product.in_stock ? 'В корзину' : 'Нет в наличии'}
                </button>
            </div>
        </div>
    );
}
