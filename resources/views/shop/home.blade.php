@extends('layouts.shop')

@section('title', $shopSite['name'] ?? 'Магазин')

@section('content')
    <section class="shop-hero">
        <div>
            <p class="shop-kicker">Интернет-магазин</p>
            <h1>{{ $site['name'] ?? 'Магазин' }}</h1>
            <p class="shop-lead">{{ $site['description'] ?? '' }}</p>
            <a class="shop-btn" href="{{ route('shop.catalog') }}">Смотреть каталог</a>
        </div>
    </section>

    <section class="mt-12">
        <h2 class="shop-h2">Категории</h2>
        <div class="shop-grid-cats">
            @foreach ($categories as $category)
                <a class="shop-cat-card" href="{{ route('shop.category', $category->slug) }}">
                    <strong>{{ $category->name }}</strong>
                    <span>{{ $category->products_count }} товаров</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mt-12">
        <h2 class="shop-h2">Популярные товары</h2>
        <div class="shop-grid-products">
            @foreach ($products as $product)
                @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>
@endsection
