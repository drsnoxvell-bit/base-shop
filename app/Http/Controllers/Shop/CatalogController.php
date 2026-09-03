<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Shop\CartService;
use App\Services\Shop\SettingService;
use App\Support\ShopPayload;
use App\Support\Storefront;

class CatalogController extends Controller
{
    public function home(SettingService $settings)
    {
        $categories = Category::query()->active()->ordered()->withCount(['products' => fn ($q) => $q->active()])->get();
        $products = Product::query()->active()->with(['category', 'attachment'])->ordered()->limit(8)->get();

        return Storefront::respond('shop.home', 'Home', [
            'site' => $settings->site(),
            'categories' => $categories,
            'products' => $products,
        ], [
            'site' => $settings->site(),
            'categories' => $categories->map(fn (Category $category) => ShopPayload::category($category))->values()->all(),
            'products' => $products->map(fn (Product $product) => ShopPayload::productCard($product))->values()->all(),
        ]);
    }

    public function catalog()
    {
        $products = Product::query()->active()->with(['category', 'attachment'])->ordered()->paginate(12);

        return Storefront::respond('shop.catalog', 'Catalog', [
            'products' => $products,
        ], [
            'products' => ShopPayload::paginator($products, fn (Product $product) => ShopPayload::productCard($product)),
        ]);
    }

    public function category(string $slug)
    {
        $category = Category::query()->active()->where('slug', $slug)->firstOrFail();
        $products = $category->products()->active()->with(['category', 'attachment'])->ordered()->paginate(12);

        return Storefront::respond('shop.category', 'Category', compact('category', 'products'), [
            'category' => ShopPayload::category($category),
            'products' => ShopPayload::paginator($products, fn (Product $product) => ShopPayload::productCard($product)),
        ]);
    }

    public function product(string $slug)
    {
        $product = Product::query()
            ->active()
            ->with(['category', 'attachment'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::query()
            ->active()
            ->with(['category', 'attachment'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->ordered()
            ->limit(4)
            ->get();

        $gallery = $product->attachments('gallery')->get();
        if ($gallery->isEmpty()) {
            $gallery = $product->attachments()->get();
        }

        return Storefront::respond('shop.product', 'Product', [
            'product' => $product,
            'related' => $related,
            'gallery' => $gallery,
            'metaDescription' => \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 160),
        ], [
            'product' => ShopPayload::product($product, $gallery, $related),
        ]);
    }

    public function bootstrap(SettingService $settings, CartService $cart)
    {
        return response()->json([
            'shopSite' => $settings->site(),
            'cartCount' => $cart->count(),
            'navCategories' => Category::query()->active()->ordered()->get(['id', 'name', 'slug']),
            'auth' => [
                'user' => ShopPayload::user(request()->user()),
            ],
            'currency' => config('shop.currency'),
            'stack' => config('shop.stack'),
        ]);
    }
}
