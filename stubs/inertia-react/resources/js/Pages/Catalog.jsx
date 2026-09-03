import { Link } from '@inertiajs/react';
import ProductCard from '../Components/ProductCard';

export default function Catalog({ products }) {
    return (
        <>
            <h1 className="shop-h1">Каталог</h1>
            <div className="shop-grid-products">
                {(products.data || []).map((product) => <ProductCard key={product.id} product={product} />)}
                {!products.data?.length && <p>Товаров пока нет.</p>}
            </div>
        </>
    );
}
