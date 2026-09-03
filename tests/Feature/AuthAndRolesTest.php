<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\ShopPermissions;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Orchid\Platform\Models\Role;
use Tests\TestCase;

class AuthAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_login_and_register_pages_load(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Яндекс');
        $this->get(route('register'))->assertOk()->assertSee('ВКонтакте');
    }

    public function test_user_can_register_and_gets_user_role(): void
    {
        $this->post(route('register'), [
            'name' => 'Покупатель',
            'email' => 'buyer@example.test',
            'password' => 'password12',
            'password_confirmation' => 'password12',
        ])->assertRedirect(route('shop.home'));

        $user = User::query()->where('email', 'buyer@example.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->inRole(ShopPermissions::ROLE_USER));
        $this->assertFalse($user->canAccessAdmin());
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.test',
            'password' => bcrypt('password12'),
        ]);

        $this->post(route('login'), [
            'email' => 'login@example.test',
            'password' => 'password12',
        ])->assertRedirect(route('shop.home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_oauth_creates_user_when_missing(): void
    {
        $social = Mockery::mock(SocialUser::class);
        $social->shouldReceive('getId')->andReturn('42');
        $social->shouldReceive('getEmail')->andReturn(null);
        $social->shouldReceive('getName')->andReturn('VK Пользователь');

        $driver = Mockery::mock();
        $driver->shouldReceive('user')->andReturn($social);
        Socialite::shouldReceive('driver')->with('vkontakte')->andReturn($driver);

        $this->get(route('auth.social.callback', 'vkontakte'))->assertRedirect(route('shop.home'));

        $this->assertDatabaseHas('users', ['email' => 'vkontakte-42@users.local']);
        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'vkontakte',
            'provider_id' => '42',
        ]);
        $this->assertTrue(User::query()->where('email', 'vkontakte-42@users.local')->first()->inRole('user'));
    }

    public function test_oauth_logs_in_existing_email(): void
    {
        $user = User::factory()->create(['email' => 'ya@example.test']);

        $social = Mockery::mock(SocialUser::class);
        $social->shouldReceive('getId')->andReturn('ya-1');
        $social->shouldReceive('getEmail')->andReturn('ya@example.test');
        $social->shouldReceive('getName')->andReturn('Яндекс');

        $driver = Mockery::mock();
        $driver->shouldReceive('user')->andReturn($social);
        Socialite::shouldReceive('driver')->with('yandex')->andReturn($driver);

        $this->get(route('auth.social.callback', 'yandex'))->assertRedirect(route('shop.home'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('social_accounts', ['provider' => 'yandex', 'provider_id' => 'ya-1', 'user_id' => $user->id]);
    }

    public function test_editor_can_open_catalog_but_not_settings(): void
    {
        $user = User::factory()->create();
        $user->assignShopRole(ShopPermissions::ROLE_EDITOR);

        $this->actingAs($user)->get(route('platform.shop.products'))->assertOk();
        $this->actingAs($user)->get(route('platform.shop.orders'))->assertOk();
        $this->actingAs($user)->get(route('platform.shop.settings'))->assertForbidden();
        $this->actingAs($user)->get(route('platform.systems.users'))->assertForbidden();
    }

    public function test_storefront_user_cannot_open_admin(): void
    {
        $user = User::factory()->create();
        $user->assignShopRole(ShopPermissions::ROLE_USER);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_roles_are_seeded(): void
    {
        $this->assertTrue(Role::query()->where('slug', 'administrator')->exists());
        $this->assertTrue(Role::query()->where('slug', 'editor')->exists());
        $this->assertTrue(Role::query()->where('slug', 'user')->exists());
    }

    public function test_shop_install_blade_keep_stubs(): void
    {
        $this->artisan('shop:install', [
            '--stack' => 'blade',
            '--keep-stubs' => true,
            '--skip-packages' => true,
            '--skip-migrate' => true,
            '--skip-admin' => true,
            '--no-npm' => true,
            '--no-interaction' => true,
        ])->assertSuccessful();

        $this->assertDirectoryExists(base_path('stubs/blade'));
        $this->assertSame('blade', config('shop.stack'));
    }
}
