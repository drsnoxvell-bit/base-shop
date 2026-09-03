@extends('layouts.shop')

@section('title', 'Корзина')

@section('content')
    <h1 class="shop-h1">Корзина</h1>

    @if ($count < 1)
        <p>Корзина пуста. <a href="{{ route('shop.catalog') }}">Перейти в каталог</a></p>
    @else
        <form method="post" action="{{ route('shop.cart.recalculate') }}" class="mb-4">
            @csrf
            <button class="shop-btn-ghost" type="submit">Пересчитать</button>
        </form>

        <div class="shop-table-wrap">
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
                        <tr>
                            <td>
                                <a href="{{ route('shop.product', $line['product']->slug) }}">{{ $line['product']->name }}</a>
                            </td>
                            <td>{{ shop_money($line['product']->price) }}</td>
                            <td>
                                <form method="post" action="{{ route('shop.cart.update', $line['product']) }}" class="shop-qty-form">
                                    @csrf
                                    @method('patch')
                                    <input type="number" name="qty" min="0" max="{{ $line['product']->quantity }}" value="{{ $line['qty'] }}">
                                    <button type="submit">OK</button>
                                </form>
                            </td>
                            <td>{{ shop_money($line['sum']) }}</td>
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
            <div>Итого: <strong>{{ shop_money($total) }}</strong></div>
            <a class="shop-btn" href="{{ route('shop.checkout') }}">Оформить заказ</a>
        </div>
    @endif
@endsection
