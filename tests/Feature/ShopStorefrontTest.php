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

        $this->patchJson(route('shop.cart.update', $product), ['qty' => 3])
            ->assertOk()
            ->assertJsonPath('count', 3)
            ->assertJsonPath('lines.0.qty', 3);
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

    public function test_product_card_shows_sku_stock_and_discount(): void
    {
        $product = Product::query()->active()->whereNotNull('old_price')->firstOrFail();

        $this->get(route('shop.catalog'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($product->sku)
            ->assertSee('Артикул')
            ->assertSee('В корзину')
            ->assertSee('Подробнее');
    }

    public function test_api_home_returns_json(): void
    {
        $this->getJson('/api/shop/home')
            ->assertOk()
            ->assertJsonStructure([
                'site',
                'categories',
                'products' => [[
                    'id',
                    'name',
                    'slug',
                    'sku',
                    'excerpt',
                    'price_formatted',
                    'old_price_formatted',
                    'discount_percent',
                    'savings_formatted',
                    'stock_status',
                    'stock_label',
                    'photos_count',
                    'cover_url',
                    'category' => ['id', 'name', 'slug'],
                ]],
            ]);
    }
}
