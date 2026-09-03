<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Support\ShopPayload;
use App\Support\Storefront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $orders = $user->orders()->latest()->with('items')->limit(20)->get();

        return Storefront::respond('shop.account', 'Account', [
            'user' => $user,
            'orders' => $orders,
        ], [
            'user' => ShopPayload::user($user),
            'orders' => $orders->map(fn ($order) => ShopPayload::order($order))->values()->all(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email,'.Auth::id()],
        ]);

        $request->user()->update($data);

        if (Storefront::wantsJson($request)) {
            return response()->json(['user' => ShopPayload::user($request->user()->fresh())]);
        }

        return back()->with('success', 'Профиль сохранён.');
    }
}
