@extends('layouts.shop')

@section('title', 'Каталог')

@section('content')
    <h1 class="shop-h1">Каталог</h1>
    <div class="shop-grid-products">
        @forelse ($products as $product)
            @include('shop.partials.product-card', ['product' => $product])
        @empty
            <p>Товаров пока нет.</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
@endsection
