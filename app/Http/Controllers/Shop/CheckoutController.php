<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Shop\CartService;
use App\Services\Shop\OrderService;
use App\Support\ShopPayload;
use App\Support\Storefront;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OrderService $orders,
    ) {}

    public function create()
    {
        $this->cart->recalculate();
        $summary = $this->cart->summary();

        if ($summary['count'] < 1) {
            if (Storefront::wantsJson()) {
                return response()->json(['message' => 'Добавьте товары в корзину.'], 422);
            }

            return redirect()->route('shop.cart')->with('error', 'Добавьте товары в корзину.');
        }

        $user = request()->user();

        return Storefront::respond('shop.checkout', 'Checkout', array_merge($summary, [
            'user' => $user,
        ]), array_merge(ShopPayload::cart($summary), [
            'user' => ShopPayload::user($user),
        ]));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = $this->orders->checkout($data);

        if (Storefront::wantsJson($request)) {
            return response()->json([
                'order' => ShopPayload::order($order),
                'redirect' => route('shop.checkout.success', $order),
            ]);
        }

        return redirect()->route('shop.checkout.success', $order);
    }

    public function success(Order $order)
    {
        $order->load('items');

        return Storefront::respond('shop.success', 'Success', compact('order'), [
            'order' => ShopPayload::order($order),
        ]);
    }
}
