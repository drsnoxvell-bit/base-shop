@extends('layouts.shop')

@section('title', 'Профиль')

@section('content')
    <h1 class="shop-h1">Профиль</h1>

    <form method="post" action="{{ route('shop.account.update') }}" class="shop-form" style="max-width: 28rem">
        @csrf
        @method('put')
        <label>Имя
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </label>
        <button class="shop-btn" type="submit">Сохранить</button>
    </form>

    <h2 class="shop-h2 mt-12">Мои заказы</h2>
    @forelse ($orders as $order)
        <div class="shop-summary mb-4">
            <div class="shop-summary-row">
                <strong>{{ $order->number }}</strong>
                <span>{{ shop_money($order->total) }}</span>
            </div>
            <p class="shop-muted">{{ $order->created_at?->format('d.m.Y H:i') }} · {{ $order->status->value ?? $order->status }}</p>
        </div>
    @empty
        <p>Заказов пока нет.</p>
    @endforelse
@endsection
