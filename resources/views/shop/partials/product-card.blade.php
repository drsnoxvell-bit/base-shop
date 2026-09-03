<article class="shop-product-card">
    <a href="{{ route('shop.product', $product->slug) }}" class="shop-product-media">
        @if ($product->coverUrl())
            <img src="{{ $product->coverUrl() }}" alt="{{ $product->name }}">
        @else
            <span class="shop-photo-empty">Нет фото</span>
        @endif
        <div class="shop-product-badges">
            @if ($product->discountPercent())
                <span class="shop-badge shop-badge-sale">−{{ $product->discountPercent() }}%</span>
            @endif
            <span class="shop-badge shop-badge-stock is-{{ $product->stockStatus() }}">{{ $product->stockLabel() }}</span>
            @if ($product->photosCount() > 1)
                <span class="shop-badge shop-badge-photos">{{ $product->photosCount() }} фото</span>
            @endif
        </div>
    </a>
    <div class="shop-product-body">
        @if ($product->category)
            <a class="shop-product-cat" href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a>
        @endif
        <a href="{{ route('shop.product', $product->slug) }}" class="shop-product-name">{{ $product->name }}</a>
        @if ($product->excerpt())
            <p class="shop-product-excerpt">{{ $product->excerpt() }}</p>
        @endif
        <dl class="shop-product-meta">
            @if ($product->sku)
                <div>
                    <dt>Артикул</dt>
                    <dd>{{ $product->sku }}</dd>
                </div>
            @endif
            <div>
                <dt>Склад</dt>
                <dd>{{ $product->quantity }} шт.</dd>
            </div>
        </dl>
        <div class="shop-product-price">
            <span class="shop-product-price-now">{{ shop_money($product->price) }}</span>
            @if ($product->old_price)
                <s>{{ shop_money($product->old_price) }}</s>
            @endif
            @if ($product->savings())
                <span class="shop-product-save">Выгода {{ shop_money($product->savings()) }}</span>
            @endif
        </div>
        <div class="shop-product-actions">
            <a class="shop-btn-ghost shop-btn-sm" href="{{ route('shop.product', $product->slug) }}">Подробнее</a>
            <form method="post" action="{{ route('shop.cart.add', $product) }}">
                @csrf
                <input type="hidden" name="qty" value="1">
                <button class="shop-btn shop-btn-sm" type="submit" @disabled(! $product->inStock())>
                    {{ $product->inStock() ? 'В корзину' : 'Нет в наличии' }}
                </button>
            </form>
        </div>
    </div>
</article>
