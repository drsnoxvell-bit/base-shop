<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Shop\AuthService;
use App\Support\ShopStack;
use App\Support\Storefront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        if (ShopStack::isSpa()) {
            return redirect('/register');
        }

        return Storefront::respond('auth.register', 'Auth/Register', []);
    }

    public function store(Request $request, AuthService $auth)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $auth->register($data);
        Auth::login($user);
        $request->session()->regenerate();

        return Storefront::afterAuth($request);
    }
}
