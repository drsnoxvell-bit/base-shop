import { Link } from '@inertiajs/react';
import ProductCard from '../Components/ProductCard';

export default function Category({ category, products }) {
    return (
        <>
            <p className="shop-crumb"><Link href="/catalog">Каталог</Link> / {category.name}</p>
            <h1 className="shop-h1">{category.name}</h1>
            {category.description && <p className="shop-lead">{category.description}</p>}
            <div className="shop-grid-products">
                {(products.data || []).map((product) => <ProductCard key={product.id} product={product} />)}
            </div>
        </>
    );
}
