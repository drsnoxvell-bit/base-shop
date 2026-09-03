import { Link } from '@inertiajs/react';

export default function Success({ order }) {
    return (
        <div className="shop-success">
            <h1 className="shop-h1">Спасибо!</h1>
            <p>Заказ <strong>{order.number}</strong> принят. Мы свяжемся с вами для подтверждения.</p>
            <p>Сумма: {order.total_formatted}</p>
            <Link href="/" className="shop-btn">На главную</Link>
        </div>
    );
}
