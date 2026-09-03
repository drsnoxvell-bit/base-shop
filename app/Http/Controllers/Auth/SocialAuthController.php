<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Shop\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): SymfonyRedirect
    {
        $this->assertProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, AuthService $auth): RedirectResponse
    {
        $this->assertProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->with('error', 'Не удалось войти через социальную сеть. Попробуйте ещё раз.');
        }

        $user = $auth->fromSocial($provider, $socialUser);
        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('shop.home'));
    }

    private function assertProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['yandex', 'vkontakte'], true), 404);
    }
}
