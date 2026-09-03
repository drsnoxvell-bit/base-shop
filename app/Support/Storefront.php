<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class Storefront
{
    public static function respond(string $view, string $component, array $viewData, array $pageData = []): View|JsonResponse|Response
    {
        if (self::wantsJson()) {
            return response()->json($pageData !== [] ? $pageData : $viewData);
        }

        if (ShopStack::isInertia()) {
            if (! class_exists(\Inertia\Inertia::class)) {
                throw new RuntimeException('Пакет inertiajs/inertia-laravel не установлен. Запустите php artisan shop:install --stack=inertia-vue');
            }

            return \Inertia\Inertia::render($component, $pageData !== [] ? $pageData : $viewData);
        }

        return view($view, $viewData);
    }

    public static function wantsJson(?Request $request = null): bool
    {
        $request ??= request();

        return $request->is('api/*') || $request->expectsJson();
    }

    public static function afterAuth(?Request $request = null): RedirectResponse|JsonResponse
    {
        $request ??= request();

        if (self::wantsJson($request) || ShopStack::isSpa()) {
            return response()->json(['user' => ShopPayload::user($request->user())]);
        }

        return redirect()->intended(route('shop.home'));
    }
}
