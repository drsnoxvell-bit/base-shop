@extends('layouts.shop')

@section('title', $product->name)
@section('description', $metaDescription)

@section('content')
    <p class="shop-crumb">
        <a href="{{ route('shop.catalog') }}">Каталог</a>
        @if ($product->category)
            / <a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a>
        @endif
        / {{ $product->name }}
    </p>

    <div class="shop-product-page">
        <div>
            @if ($gallery->isNotEmpty())
                <div class="swiper product-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($gallery as $image)
                            <div class="swiper-slide">
                                <a class="glightbox" href="{{ $image->url() }}">
                                    <img src="{{ $image->url() }}" alt="{{ $product->name }}">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if ($gallery->count() > 1)
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    @endif
                </div>
            @else
                <div class="shop-photo-empty shop-photo-lg">Нет фото</div>
            @endif
        </div>
        <div>
            <h1 class="shop-h1">{{ $product->name }}</h1>
            @if ($product->sku)
                <p class="shop-muted">Артикул: {{ $product->sku }}</p>
            @endif
            <div class="shop-price-row shop-price-lg">
                <strong>{{ shop_money($product->price) }}</strong>
                @if ($product->old_price)
                    <s>{{ shop_money($product->old_price) }}</s>
                @endif
            </div>
            <p class="mt-4">{{ $product->description }}</p>
            <p class="mt-3 shop-muted">На складе: {{ $product->quantity }} шт.</p>
            <form method="post" action="{{ route('shop.cart.add', $product) }}" class="shop-add-form">
                @csrf
                <label>
                    Количество
                    <input type="number" name="qty" min="1" max="{{ max(1, $product->quantity) }}" value="1">
                </label>
                <button class="shop-btn" type="submit" @disabled(! $product->inStock())>
                    {{ $product->inStock() ? 'Добавить в корзину' : 'Нет в наличии' }}
                </button>
            </form>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h2 class="shop-h2 mt-12">Похожие товары</h2>
        <div class="shop-grid-products">
            @foreach ($related as $item)
                @include('shop.partials.product-card', ['product' => $item])
            @endforeach
        </div>
    @endif
@endsection
