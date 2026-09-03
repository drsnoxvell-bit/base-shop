<?php

namespace App\Services\Shop;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingService
{
    public const SITE_KEY = 'site';

    public const MAIL_KEY = 'mail';

    public function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key, 2);
        $group = $parts[0];
        $path = $parts[1] ?? null;
        $data = $this->group($group);

        if ($path === null) {
            return $data !== [] ? $data : $default;
        }

        return Arr::get($data, $path, $default);
    }

    public function site(): array
    {
        return array_merge($this->defaultSite(), $this->group(self::SITE_KEY));
    }

    public function mail(): array
    {
        return array_merge($this->defaultMail(), $this->group(self::MAIL_KEY));
    }

    public function saveSite(array $data): void
    {
        $this->put(self::SITE_KEY, array_merge($this->site(), Arr::only($data, [
            'name', 'description', 'phone', 'email', 'address',
        ])));
    }

    public function saveMail(array $data): void
    {
        $current = $this->mail();

        if (($data['password'] ?? '') === '') {
            $data['password'] = $current['password'] ?? '';
        }

        $this->put(self::MAIL_KEY, array_merge($current, Arr::only($data, [
            'mailer', 'host', 'port', 'username', 'password', 'encryption', 'from_address', 'from_name',
        ])));
    }

    public function applyMailConfig(): void
    {
        if (! $this->available()) {
            return;
        }

        $mail = $this->mail();

        if (blank($mail['host'] ?? null)) {
            return;
        }

        config([
            'mail.default' => $mail['mailer'] ?: 'smtp',
            'mail.mailers.smtp.host' => $mail['host'],
            'mail.mailers.smtp.port' => (int) ($mail['port'] ?: 587),
            'mail.mailers.smtp.username' => $mail['username'] ?: null,
            'mail.mailers.smtp.password' => $mail['password'] ?: null,
            'mail.mailers.smtp.encryption' => $mail['encryption'] ?: null,
            'mail.from.address' => $mail['from_address'] ?: config('mail.from.address'),
            'mail.from.name' => $mail['from_name'] ?: config('mail.from.name'),
        ]);
    }

    public function put(string $key, array $value): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        Cache::forget($this->cacheKey($key));
    }

    private function group(string $key): array
    {
        if (! $this->available()) {
            return [];
        }

        return Cache::remember($this->cacheKey($key), 60, function () use ($key) {
            $row = Setting::query()->where('key', $key)->first();

            return is_array($row?->value) ? $row->value : [];
        });
    }

    private function available(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }

    private function cacheKey(string $key): string
    {
        return 'shop.settings.'.$key;
    }

    private function defaultSite(): array
    {
        return [
            'name' => config('app.name', 'Магазин'),
            'description' => 'Интернет-магазин на Laravel и Orchid',
            'phone' => '',
            'email' => config('mail.from.address'),
            'address' => '',
        ];
    }

    private function defaultMail(): array
    {
        return [
            'mailer' => config('mail.default', 'log'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => (string) config('mail.mailers.smtp.port', 587),
            'username' => (string) config('mail.mailers.smtp.username'),
            'password' => (string) config('mail.mailers.smtp.password'),
            'encryption' => (string) (config('mail.mailers.smtp.encryption') ?? ''),
            'from_address' => (string) config('mail.from.address'),
            'from_name' => (string) config('mail.from.name'),
        ];
    }
}
