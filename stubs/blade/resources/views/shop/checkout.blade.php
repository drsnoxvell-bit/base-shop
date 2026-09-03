@extends('layouts.shop')

@section('title', 'Оформление заказа')

@section('content')
    <h1 class="shop-h1">Оформление заказа</h1>
    <p class="shop-muted">Оплата при получении. После отправки заявки с вами свяжется менеджер.</p>

    <div class="shop-checkout">
        <form method="post" action="{{ route('shop.checkout.store') }}" class="shop-form">
            @csrf
            <label>Имя
                <input type="text" name="name" value="{{ old('name', $user?->name) }}" required>
            </label>
            <label>Телефон
                <input type="text" name="phone" value="{{ old('phone') }}" required>
            </label>
            <label>Email
                <input type="email" name="email" value="{{ old('email', $user?->email) }}">
            </label>
            <label>Адрес доставки
                <textarea name="address" rows="3" required>{{ old('address') }}</textarea>
            </label>
            <label>Комментарий
                <textarea name="comment" rows="3">{{ old('comment') }}</textarea>
            </label>
            <button class="shop-btn" type="submit">Подтвердить заказ</button>
        </form>

        <aside class="shop-summary">
            <h2>Ваш заказ</h2>
            @foreach ($lines as $line)
                <div class="shop-summary-row">
                    <span>{{ $line['product']->name }} × {{ $line['qty'] }}</span>
                    <span>{{ shop_money($line['sum']) }}</span>
                </div>
            @endforeach
            <div class="shop-summary-row shop-summary-total">
                <span>Итого</span>
                <strong>{{ shop_money($total) }}</strong>
            </div>
        </aside>
    </div>
@endsection
