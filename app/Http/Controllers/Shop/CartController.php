<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Shop\CartService;
use App\Support\ShopPayload;
use App\Support\Storefront;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function show()
    {
        $this->cart->recalculate();
        $summary = $this->cart->summary();

        return Storefront::respond('shop.cart', 'Cart', $summary, ShopPayload::cart($summary));
    }

    public function add(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $this->cart->add($product, (int) ($data['qty'] ?? 1));

        if (Storefront::wantsJson($request)) {
            return response()->json($this->cart->json());
        }

        return back()->with('success', 'Товар добавлен в корзину.');
    }

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($product, (int) $data['qty']);

        if (Storefront::wantsJson($request)) {
            return response()->json($this->cart->json());
        }

        return back()->with('success', 'Корзина обновлена.');
    }

    public function remove(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $this->cart->remove($product->id);

        if (Storefront::wantsJson($request)) {
            return response()->json($this->cart->json());
        }

        return back()->with('success', 'Товар удалён из корзины.');
    }

    public function recalculate(Request $request): RedirectResponse|JsonResponse
    {
        $this->cart->recalculate();

        if (Storefront::wantsJson($request)) {
            return response()->json($this->cart->json());
        }

        return back()->with('success', 'Сумма и количество пересчитаны.');
    }
}
