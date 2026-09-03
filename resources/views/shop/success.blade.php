@extends('layouts.shop')

@section('title', 'Заказ принят')

@section('content')
    <div class="shop-success">
        <h1 class="shop-h1">Спасибо!</h1>
        <p>Заказ <strong>{{ $order->number }}</strong> принят. Мы свяжемся с вами для подтверждения.</p>
        <p>Сумма: {{ shop_money($order->total) }}</p>
        <a class="shop-btn" href="{{ route('shop.home') }}">На главную</a>
    </div>
@endsection
