<?php

namespace App\Services\Shop;

use App\Models\SocialAccount;
use App\Models\User;
use App\Support\ShopPermissions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;
use Orchid\Platform\Models\Role;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->assignRole($user, ShopPermissions::ROLE_USER);

        return $user;
    }

    public function fromSocial(string $provider, SocialUser $socialUser): User
    {
        $account = SocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_id', (string) $socialUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        }

        $email = $this->resolveEmail($provider, $socialUser);
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $socialUser->getName() ?: 'Пользователь',
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => $socialUser->getEmail() ? now() : null,
            ]);

            $this->assignRole($user, ShopPermissions::ROLE_USER);
        }

        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => (string) $socialUser->getId(),
        ]);

        return $user;
    }

    public function assignRole(User $user, string $slug): void
    {
        $role = Role::query()->where('slug', $slug)->first();

        if ($role) {
            $user->replaceRoles([$role->id]);
        }
    }

    public function assignAdministrator(User $user): void
    {
        $this->assignRole($user, ShopPermissions::ROLE_ADMINISTRATOR);
        $user->forceFill([
            'permissions' => ShopPermissions::administrator(),
        ])->save();
    }

    private function resolveEmail(string $provider, SocialUser $socialUser): string
    {
        $email = $socialUser->getEmail();

        if (filled($email)) {
            return $email;
        }

        return $provider.'-'.$socialUser->getId().'@users.local';
    }
}
