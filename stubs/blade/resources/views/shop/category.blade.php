@extends('layouts.shop')

@section('title', $category->name)
@section('description', $category->description)

@section('content')
    <p class="shop-crumb"><a href="{{ route('shop.catalog') }}">Каталог</a> / {{ $category->name }}</p>
    <h1 class="shop-h1">{{ $category->name }}</h1>
    @if ($category->description)
        <p class="shop-lead">{{ $category->description }}</p>
    @endif
    <div class="shop-grid-products">
        @forelse ($products as $product)
            @include('shop.partials.product-card', ['product' => $product])
        @empty
            <p>В этой категории пока нет товаров.</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
@endsection
