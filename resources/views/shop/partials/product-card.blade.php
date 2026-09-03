<div class="shop-product-card">
    <a href="{{ route('shop.product', $product->slug) }}" class="shop-product-photo">
        @if ($product->coverUrl())
            <img src="{{ $product->coverUrl() }}" alt="{{ $product->name }}">
        @else
            <span class="shop-photo-empty">Нет фото</span>
        @endif
    </a>
    <div class="shop-product-body">
        <a href="{{ route('shop.product', $product->slug) }}" class="shop-product-name">{{ $product->name }}</a>
        @if ($product->category)
            <div class="shop-muted">{{ $product->category->name }}</div>
        @endif
        <div class="shop-price-row">
            <strong>{{ shop_money($product->price) }}</strong>
            @if ($product->old_price)
                <s>{{ shop_money($product->old_price) }}</s>
            @endif
        </div>
        <form method="post" action="{{ route('shop.cart.add', $product) }}" class="mt-3">
            @csrf
            <input type="hidden" name="qty" value="1">
            <button class="shop-btn shop-btn-sm" type="submit" @disabled(! $product->inStock())>
                {{ $product->inStock() ? 'В корзину' : 'Нет в наличии' }}
            </button>
        </form>
    </div>
</div>
