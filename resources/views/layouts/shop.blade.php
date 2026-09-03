<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $shopSite['name'] ?? config('app.name'))</title>
    <meta name="description" content="@yield('description', $shopSite['description'] ?? '')">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="shop-body min-h-screen flex flex-col">
    <header class="shop-header">
        <div class="shop-wrap flex items-center justify-between gap-4 py-4">
            <a href="{{ route('shop.home') }}" class="shop-logo">{{ $shopSite['name'] ?? 'Магазин' }}</a>
            <nav class="shop-nav hidden md:flex items-center gap-6">
                <a href="{{ route('shop.home') }}">Главная</a>
                <a href="{{ route('shop.catalog') }}">Каталог</a>
                @foreach ($navCategories as $category)
                    <a href="{{ route('shop.category', $category->slug) }}">{{ $category->name }}</a>
                @endforeach
            </nav>
            <a href="{{ route('shop.cart') }}" class="shop-cart-link">
                Корзина
                <span class="shop-cart-badge">{{ $cartCount }}</span>
            </a>
        </div>
        <nav class="shop-nav-mobile md:hidden shop-wrap pb-3 flex flex-wrap gap-3">
            <a href="{{ route('shop.catalog') }}">Каталог</a>
            @foreach ($navCategories as $category)
                <a href="{{ route('shop.category', $category->slug) }}">{{ $category->name }}</a>
            @endforeach
        </nav>
    </header>

    <main class="flex-1">
        <div class="shop-wrap py-8">
            @if (session('success'))
                <div class="shop-alert shop-alert-ok">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="shop-alert shop-alert-err">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="shop-alert shop-alert-err">
                    {{ $errors->first() }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="shop-footer">
        <div class="shop-wrap py-8 grid gap-6 md:grid-cols-3">
            <div>
                <div class="shop-logo">{{ $shopSite['name'] ?? 'Магазин' }}</div>
                <p class="mt-2 text-sm opacity-80">{{ $shopSite['description'] ?? '' }}</p>
            </div>
            <div>
                <div class="font-semibold mb-2">Контакты</div>
                @if (!empty($shopSite['phone']))
                    <p>{{ $shopSite['phone'] }}</p>
                @endif
                @if (!empty($shopSite['email']))
                    <p>{{ $shopSite['email'] }}</p>
                @endif
            </div>
            <div>
                <div class="font-semibold mb-2">Адрес</div>
                <p>{{ $shopSite['address'] ?? '' }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
