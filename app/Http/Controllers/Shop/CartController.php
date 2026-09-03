<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Shop\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function show(): View
    {
        $this->cart->recalculate();

        return view('shop.cart', $this->cart->summary());
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $this->cart->add($product, (int) ($data['qty'] ?? 1));

        return back()->with('success', 'Товар добавлен в корзину.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($product, (int) $data['qty']);
        $this->cart->recalculate();

        return back()->with('success', 'Корзина обновлена.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $this->cart->remove($product->id);

        return back()->with('success', 'Товар удалён из корзины.');
    }

    public function recalculate(): RedirectResponse
    {
        $this->cart->recalculate();

        return back()->with('success', 'Сумма и количество пересчитаны.');
    }
}
