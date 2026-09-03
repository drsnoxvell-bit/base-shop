<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Shop\SettingService;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function home(SettingService $settings): View
    {
        $categories = Category::query()->active()->ordered()->withCount(['products' => fn ($q) => $q->active()])->get();
        $products = Product::query()->active()->with(['category', 'attachment'])->ordered()->limit(8)->get();

        return view('shop.home', [
            'site' => $settings->site(),
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function catalog(): View
    {
        $products = Product::query()->active()->with(['category', 'attachment'])->ordered()->paginate(12);

        return view('shop.catalog', [
            'products' => $products,
        ]);
    }

    public function category(string $slug): View
    {
        $category = Category::query()->active()->where('slug', $slug)->firstOrFail();
        $products = $category->products()->active()->with('attachment')->ordered()->paginate(12);

        return view('shop.category', compact('category', 'products'));
    }

    public function product(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->with(['category', 'attachment'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::query()
            ->active()
            ->with('attachment')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->ordered()
            ->limit(4)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }
}
