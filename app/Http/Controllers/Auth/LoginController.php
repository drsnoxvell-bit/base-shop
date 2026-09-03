<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ShopStack;
use App\Support\Storefront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        if (ShopStack::isSpa()) {
            return redirect('/login');
        }

        return Storefront::respond('auth.login', 'Auth/Login', [], [
            'canRegister' => true,
        ]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Неверный email или пароль.',
            ]);
        }

        $request->session()->regenerate();

        return Storefront::afterAuth($request);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (Storefront::wantsJson($request) || ShopStack::isSpa()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('shop.home');
    }
}
