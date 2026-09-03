import { Link, router } from '@inertiajs/react';

export default function Cart({ empty, lines = [], total_formatted }) {
    const setQty = (line, qty) => {
        if (qty > line.max) return;
        router.patch(`/cart/${line.id}`, { qty });
    };

    if (empty) {
        return <p>Корзина пуста. <Link href="/catalog">Перейти в каталог</Link></p>;
    }

    return (
        <>
            <h1 className="shop-h1">Корзина</h1>
            <div className="shop-table-wrap">
                <table className="shop-table">
                    <thead><tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th><th></th></tr></thead>
                    <tbody>
                        {lines.map((line) => (
                            <tr key={line.id}>
                                <td><Link href={`/product/${line.slug}`}>{line.name}</Link></td>
                                <td>{line.price_formatted}</td>
                                <td>
                                    <div className="shop-qty">
                                        <button type="button" className="shop-qty-btn" onClick={() => setQty(line, line.qty - 1)}>−</button>
                                        <span className="shop-qty-value">{line.qty}</span>
                                        <button type="button" className="shop-qty-btn" onClick={() => setQty(line, line.qty + 1)}>+</button>
                                    </div>
                                </td>
                                <td>{line.sum_formatted}</td>
                                <td><button type="button" className="shop-link-danger" onClick={() => setQty(line, 0)}>Удалить</button></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <div className="shop-cart-total">
                <div>Итого: <strong>{total_formatted}</strong></div>
                <Link href="/checkout" className="shop-btn">Оформить заказ</Link>
            </div>
        </>
    );
}
