<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use App\Orchid\Screens\Shop\CategoryEditScreen;
use App\Orchid\Screens\Shop\CategoryListScreen;
use App\Orchid\Screens\Shop\OrderEditScreen;
use App\Orchid\Screens\Shop\OrderListScreen;
use App\Orchid\Screens\Shop\ProductEditScreen;
use App\Orchid\Screens\Shop\ProductListScreen;
use App\Orchid\Screens\Shop\SettingsScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

Route::screen('shop/categories/create', CategoryEditScreen::class)
    ->name('platform.shop.categories.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.shop.categories')
        ->push('Создать'));

Route::screen('shop/categories/{category}/edit', CategoryEditScreen::class)
    ->name('platform.shop.categories.edit')
    ->breadcrumbs(fn (Trail $trail, $category) => $trail
        ->parent('platform.shop.categories')
        ->push($category->name));

Route::screen('shop/categories', CategoryListScreen::class)
    ->name('platform.shop.categories')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Категории', route('platform.shop.categories')));

Route::screen('shop/products/create', ProductEditScreen::class)
    ->name('platform.shop.products.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.shop.products')
        ->push('Создать'));

Route::screen('shop/products/{product}/edit', ProductEditScreen::class)
    ->name('platform.shop.products.edit')
    ->breadcrumbs(fn (Trail $trail, $product) => $trail
        ->parent('platform.shop.products')
        ->push($product->name));

Route::screen('shop/products', ProductListScreen::class)
    ->name('platform.shop.products')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Товары', route('platform.shop.products')));

Route::screen('shop/orders/{order}/edit', OrderEditScreen::class)
    ->name('platform.shop.orders.edit')
    ->breadcrumbs(fn (Trail $trail, $order) => $trail
        ->parent('platform.shop.orders')
        ->push($order->number));

Route::screen('shop/orders', OrderListScreen::class)
    ->name('platform.shop.orders')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Заказы', route('platform.shop.orders')));

Route::screen('shop/settings', SettingsScreen::class)
    ->name('platform.shop.settings')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Настройки', route('platform.shop.settings')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

// Route::screen('idea', Idea::class, 'platform.screens.idea');
