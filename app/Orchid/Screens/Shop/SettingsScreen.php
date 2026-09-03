<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Shop;

use App\Orchid\Layouts\Shop\SettingsMailLayout;
use App\Orchid\Layouts\Shop\SettingsSiteLayout;
use App\Services\Shop\SettingService;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SettingsScreen extends Screen
{
    public function query(SettingService $settings): iterable
    {
        return [
            'site' => $settings->site(),
            'mail' => $settings->mail(),
        ];
    }

    public function name(): ?string
    {
        return 'Настройки';
    }

    public function description(): ?string
    {
        return 'Сайт и SMTP. Значения из .env используются как запасной вариант.';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::tabs([
                'Сайт' => SettingsSiteLayout::class,
                'SMTP' => SettingsMailLayout::class,
            ]),
        ];
    }

    public function save(Request $request, SettingService $settings): void
    {
        $data = $request->validate([
            'site.name' => ['required', 'string', 'max:255'],
            'site.description' => ['nullable', 'string', 'max:1000'],
            'site.phone' => ['nullable', 'string', 'max:50'],
            'site.email' => ['nullable', 'email', 'max:120'],
            'site.address' => ['nullable', 'string', 'max:500'],
            'mail.mailer' => ['required', 'string', 'max:32'],
            'mail.host' => ['nullable', 'string', 'max:255'],
            'mail.port' => ['nullable', 'string', 'max:10'],
            'mail.username' => ['nullable', 'string', 'max:255'],
            'mail.password' => ['nullable', 'string', 'max:255'],
            'mail.encryption' => ['nullable', 'string', 'max:16'],
            'mail.from_address' => ['nullable', 'email', 'max:120'],
            'mail.from_name' => ['nullable', 'string', 'max:255'],
        ]);

        $settings->saveSite($data['site'] ?? []);
        $settings->saveMail($data['mail'] ?? []);
        $settings->applyMailConfig();

        Toast::info('Настройки сохранены');
    }
}
