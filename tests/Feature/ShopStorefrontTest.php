<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get(route('shop.home'))->assertOk();
    }

    public function test_catalog_page_loads(): void
    {
        $this->get(route('shop.catalog'))->assertOk();
    }

    public function test_category_and_product_pages_load(): void
    {
        $product = Product::query()->active()->with('category')->firstOrFail();

        $this->get(route('shop.category', $product->category->slug))->assertOk();
        $this->get(route('shop.product', $product->slug))->assertOk();
    }

    public function test_cart_add_update_remove_and_checkout(): void
    {
        $product = Product::query()->active()->where('quantity', '>', 1)->firstOrFail();

        $this->post(route('shop.cart.add', $product), ['qty' => 2])->assertRedirect();
        $this->get(route('shop.cart'))->assertOk()->assertSee($product->name);

        $this->patch(route('shop.cart.update', $product), ['qty' => 1])->assertRedirect();
        $this->post(route('shop.cart.recalculate'))->assertRedirect();

        $this->get(route('shop.checkout'))->assertOk();

        $this->post(route('shop.checkout.store'), [
            'name' => 'Иван Тестов',
            'phone' => '+7 900 000-00-00',
            'email' => 'buyer@example.test',
            'address' => 'Тестовый адрес',
            'comment' => 'Тест',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['name' => 'Иван Тестов']);
        $this->assertSame(0, $this->app['session']->get('shop_cart') ? array_sum($this->app['session']->get('shop_cart')) : 0);
    }

    public function test_admin_login_page_is_available(): void
    {
        $this->get('/admin/login')->assertOk();
    }
}
