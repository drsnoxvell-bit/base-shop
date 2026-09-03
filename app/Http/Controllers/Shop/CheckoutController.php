<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Shop\CartService;
use App\Services\Shop\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OrderService $orders,
    ) {}

    public function create(): View|RedirectResponse
    {
        $this->cart->recalculate();
        $summary = $this->cart->summary();

        if ($summary['count'] < 1) {
            return redirect()->route('shop.cart')->with('error', 'Добавьте товары в корзину.');
        }

        return view('shop.checkout', $summary);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = $this->orders->checkout($data);

        return redirect()->route('shop.checkout.success', $order);
    }

    public function success(Order $order): View
    {
        $order->load('items');

        return view('shop.success', compact('order'));
    }
}
