<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Shop\AuthService;
use App\Support\ShopStack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class ShopInstallCommand extends Command
{
    protected $signature = 'shop:install
                            {--stack= : blade, inertia-vue, inertia-react, spa-vue, spa-react}
                            {--keep-stubs : не удалять каталог stubs}
                            {--prune : удалить stubs даже если есть .git}
                            {--skip-packages : не выполнять composer require}
                            {--skip-migrate : не выполнять миграции}
                            {--skip-admin : не создавать администратора}
                            {--no-npm : не запускать npm install}
                            {--admin-name= : имя администратора}
                            {--admin-email= : email администратора}
                            {--admin-password= : пароль администратора}';

    protected $description = 'Выбор стека витрины, роли и первичная настройка магазина';

    public function handle(AuthService $auth): int
    {
        $stack = $this->resolveStack();

        if (! array_key_exists($stack, ShopStack::all())) {
            $this->error('Неизвестный стек: '.$stack);

            return self::FAILURE;
        }

        $this->info('Стек витрины: '.ShopStack::all()[$stack]);

        if (! $this->option('skip-packages')) {
            $this->requirePackages($stack);
        }

        $this->applyStub($stack);
        $this->writeEnv('SHOP_STACK', $stack);
        config(['shop.stack' => $stack]);

        if ($this->shouldPrune()) {
            $this->pruneStubs();
        }

        if (! $this->option('skip-migrate')) {
            $this->call('migrate', ['--force' => true, '--ansi' => true]);
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder', '--force' => true, '--ansi' => true]);
        }

        if (! $this->option('skip-admin')) {
            $this->createAdministrator($auth);
        }

        if (! $this->option('no-npm')) {
            $this->installNpm();
        }

        $this->newLine();
        $this->info('Готово. Дальше: настройте .env (БД, OAuth) и откройте сайт.');
        $this->line('Админка: /admin');
        $this->line('OAuth: YANDEX_CLIENT_ID / VKONTAKTE_CLIENT_ID в .env');

        return self::SUCCESS;
    }

    private function resolveStack(): string
    {
        $stack = $this->option('stack');

        if (is_string($stack) && $stack !== '') {
            return $stack;
        }

        if ($this->option('no-interaction')) {
            return ShopStack::BLADE;
        }

        return select(
            label: 'Что установить?',
            options: ShopStack::all(),
            default: ShopStack::BLADE,
        );
    }

    private function applyStub(string $stack): void
    {
        $stub = base_path('stubs/'.$stack);

        if (! File::isDirectory($stub)) {
            $this->error('Не найден каталог заготовок: '.$stub);

            return;
        }

        $this->cleanupFor($stack);
        File::copyDirectory($stub, base_path());
        $this->info('Скопированы файлы стека из stubs/'.$stack);
    }

    private function cleanupFor(string $stack): void
    {
        $paths = match ($stack) {
            ShopStack::BLADE => [
                'resources/js/Pages',
                'resources/js/Layouts',
                'resources/js/app.jsx',
                'resources/views/app.blade.php',
                'resources/views/spa.blade.php',
                'frontend',
                'app/Http/Middleware/HandleInertiaRequests.php',
            ],
            ShopStack::INERTIA_VUE, ShopStack::INERTIA_REACT => [
                'resources/views/shop',
                'resources/views/layouts/shop.blade.php',
                'resources/views/auth',
                'resources/views/spa.blade.php',
                'frontend',
                'resources/js/Pages',
                'resources/js/Layouts',
                'resources/js/app.jsx',
            ],
            ShopStack::SPA_VUE, ShopStack::SPA_REACT => [
                'resources/views/shop',
                'resources/views/layouts/shop.blade.php',
                'resources/views/auth',
                'resources/views/app.blade.php',
                'resources/js/Pages',
                'resources/js/Layouts',
                'resources/js/app.jsx',
                'frontend',
                'app/Http/Middleware/HandleInertiaRequests.php',
            ],
            default => [],
        };

        if ($stack === ShopStack::INERTIA_VUE) {
            $paths[] = 'resources/js/app.jsx';
        }

        foreach ($paths as $path) {
            $full = base_path($path);
            if (File::isDirectory($full)) {
                File::deleteDirectory($full);
            } elseif (File::exists($full)) {
                File::delete($full);
            }
        }
    }

    private function requirePackages(string $stack): void
    {
        $packages = ShopStack::composerPackages($stack);

        if ($packages === []) {
            return;
        }

        $composerJson = json_decode(File::get(base_path('composer.json')), true) ?: [];
        $required = $composerJson['require'] ?? [];
        $missing = array_values(array_filter($packages, fn (string $name) => ! isset($required[$name])));

        if ($missing === []) {
            return;
        }

        $this->info('Composer: '.implode(', ', $missing));

        $result = Process::timeout(600)
            ->path(base_path())
            ->run(array_merge($this->composerCommand(), ['require', '--no-interaction', ...$missing]));

        if ($result->failed()) {
            $this->warn('Не удалось поставить пакеты Composer. Установите вручную: composer require '.implode(' ', $missing));
            $this->line($result->errorOutput());
        }
    }

    /**
     * @return list<string>
     */
    private function composerCommand(): array
    {
        $bin = 'composer';

        if (is_file('C:\\ProgramData\\ComposerSetup\\bin\\composer.bat')) {
            $bin = 'C:\\ProgramData\\ComposerSetup\\bin\\composer.bat';
        }

        return [$bin];
    }

    private function shouldPrune(): bool
    {
        if ($this->option('keep-stubs')) {
            return false;
        }

        if ($this->option('prune')) {
            return true;
        }

        return ! File::isDirectory(base_path('.git'));
    }

    private function pruneStubs(): void
    {
        $stubs = base_path('stubs');

        if (File::isDirectory($stubs)) {
            File::deleteDirectory($stubs);
            $this->info('Каталог stubs удалён из установленной копии.');
        }
    }

    private function writeEnv(string $key, string $value): void
    {
        $path = base_path('.env');

        if (! File::exists($path)) {
            if (File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $path);
            } else {
                File::put($path, '');
            }
        }

        $content = File::get($path);

        if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $content)) {
            $content = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $key.'='.$value, $content) ?? $content;
        } else {
            $content = rtrim($content).PHP_EOL.$key.'='.$value.PHP_EOL;
        }

        File::put($path, $content);
    }

    private function createAdministrator(AuthService $auth): void
    {
        $name = $this->option('admin-name');
        $email = $this->option('admin-email');
        $password = $this->option('admin-password');

        if ($this->option('no-interaction') && (! $name || ! $email || ! $password)) {
            $this->comment('Администратор пропущен. Создайте: php artisan orchid:admin');

            return;
        }

        $name = is_string($name) && $name !== '' ? $name : text('Имя администратора', default: 'Администратор');
        $email = is_string($email) && $email !== '' ? $email : text('Email администратора', default: 'admin@example.test');
        $password = is_string($password) && $password !== '' ? $password : password('Пароль администратора');

        if (User::query()->where('email', $email)->exists()) {
            $user = User::query()->where('email', $email)->first();
            $auth->assignAdministrator($user);
            $this->info('Существующий пользователь назначен администратором.');

            return;
        }

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
        ]);

        $auth->assignAdministrator($user);
        $this->info('Администратор создан: '.$email);
    }

    private function installNpm(): void
    {
        if (! File::exists(base_path('package.json'))) {
            return;
        }

        $this->comment('npm install...');

        $result = Process::timeout(600)->path(base_path())->run(['npm', 'install', '--no-fund', '--no-audit']);

        if ($result->failed()) {
            $this->warn('npm install не выполнен. Запустите вручную: npm install && npm run build');
        }
    }
}
