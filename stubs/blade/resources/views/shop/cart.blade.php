@extends('layouts.shop')

@section('title', 'Корзина')

@section('content')
    <h1 class="shop-h1">Корзина</h1>

    @if ($count < 1)
        <p>Корзина пуста. <a href="{{ route('shop.catalog') }}">Перейти в каталог</a></p>
    @else
        <div class="shop-table-wrap" data-cart>
            <table class="shop-table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th>Кол-во</th>
                        <th>Сумма</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lines as $line)
                        <tr data-line="{{ $line['product']->id }}">
                            <td>
                                <a href="{{ route('shop.product', $line['product']->slug) }}">{{ $line['product']->name }}</a>
                            </td>
                            <td>{{ shop_money($line['product']->price) }}</td>
                            <td>
                                <div
                                    class="shop-qty"
                                    data-url="{{ route('shop.cart.update', $line['product']) }}"
                                    data-max="{{ $line['product']->quantity }}"
                                >
                                    <button type="button" class="shop-qty-btn" data-dir="-1" aria-label="Уменьшить">−</button>
                                    <span class="shop-qty-value">{{ $line['qty'] }}</span>
                                    <button type="button" class="shop-qty-btn" data-dir="1" aria-label="Увеличить">+</button>
                                </div>
                            </td>
                            <td class="shop-line-sum">{{ shop_money($line['sum']) }}</td>
                            <td>
                                <form method="post" action="{{ route('shop.cart.remove', $line['product']) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="shop-link-danger" type="submit">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="shop-cart-total">
            <div>Итого: <strong data-cart-total>{{ shop_money($total) }}</strong></div>
            <a class="shop-btn" href="{{ route('shop.checkout') }}">Оформить заказ</a>
        </div>
    @endif
@endsection
