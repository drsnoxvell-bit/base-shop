import { Link } from 'react-router-dom';
import { api } from '../api';
import { useShop } from '../App';

export default function ProductCard({ product }) {
    const { refresh } = useShop();

    const add = async () => {
        await api.post(`/api/shop/cart/${product.id}`, { qty: 1 });
        await refresh?.();
    };

    return (
        <article className="shop-product-card">
            <Link to={`/product/${product.slug}`} className="shop-product-media">
                {product.cover_url ? <img src={product.cover_url} alt={product.name} /> : <span className="shop-photo-empty">Нет фото</span>}
                <div className="shop-product-badges">
                    {product.discount_percent ? <span className="shop-badge shop-badge-sale">−{product.discount_percent}%</span> : null}
                    <span className={`shop-badge shop-badge-stock is-${product.stock_status}`}>{product.stock_label}</span>
                    {product.photos_count > 1 ? <span className="shop-badge shop-badge-photos">{product.photos_count} фото</span> : null}
                </div>
            </Link>
            <div className="shop-product-body">
                {product.category && <Link className="shop-product-cat" to={`/category/${product.category.slug}`}>{product.category.name}</Link>}
                <Link to={`/product/${product.slug}`} className="shop-product-name">{product.name}</Link>
                {product.excerpt && <p className="shop-product-excerpt">{product.excerpt}</p>}
                <dl className="shop-product-meta">
                    {product.sku && <div><dt>Артикул</dt><dd>{product.sku}</dd></div>}
                    <div><dt>Склад</dt><dd>{product.quantity} шт.</dd></div>
                </dl>
                <div className="shop-product-price">
                    <span className="shop-product-price-now">{product.price_formatted}</span>
                    {product.old_price_formatted && <s>{product.old_price_formatted}</s>}
                    {product.savings_formatted && <span className="shop-product-save">Выгода {product.savings_formatted}</span>}
                </div>
                <div className="shop-product-actions">
                    <Link className="shop-btn-ghost shop-btn-sm" to={`/product/${product.slug}`}>Подробнее</Link>
                    <button className="shop-btn shop-btn-sm" type="button" disabled={!product.in_stock} onClick={add}>
                        {product.in_stock ? 'В корзину' : 'Нет в наличии'}
                    </button>
                </div>
            </div>
        </article>
    );
}
