<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Shop\AuthService;
use App\Support\ShopStack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PDO;
use Throwable;

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

        if ($stack === null || ! array_key_exists($stack, ShopStack::all())) {
            $this->error('Неизвестный стек. Укажите 1–5 или blade / inertia-vue / inertia-react / spa-vue / spa-react.');

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

        $migrated = $this->migrateAndSeed();

        if ($migrated && ! $this->option('skip-admin')) {
            $this->createAdministrator($auth);
        } elseif (! $migrated && ! $this->option('skip-admin')) {
            $this->comment('Администратор пропущен: сначала поправьте БД, затем php artisan orchid:admin');
        }

        if (! $this->option('no-npm')) {
            $this->installNpm();
        }

        $this->newLine();
        $this->info($migrated ? 'Готово. Откройте сайт и админку /admin.' : 'Стек установлен, но миграции не выполнены.');
        $this->line('OAuth: YANDEX_CLIENT_ID / VKONTAKTE_CLIENT_ID в .env');

        return $migrated ? self::SUCCESS : self::FAILURE;
    }

    private function resolveStack(): ?string
    {
        $stack = $this->option('stack');

        if (is_string($stack) && $stack !== '') {
            return ShopStack::resolve($stack);
        }

        if ($this->option('no-interaction')) {
            return ShopStack::BLADE;
        }

        return ShopStack::resolve(select(
            label: 'Что установить?',
            options: ShopStack::installChoices(),
            default: '1',
        ));
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

    private function migrateAndSeed(): bool
    {
        if ($this->option('skip-migrate')) {
            return true;
        }

        try {
            $this->configureDatabase();
            $this->call('migrate', ['--force' => true, '--ansi' => true]);
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder', '--force' => true, '--ansi' => true]);

            return true;
        } catch (Throwable $e) {
            $this->error('Не удалось выполнить миграции.');
            $this->line($e->getMessage());
            $this->newLine();
            $this->comment('В OSPanel MySQL не слушает 127.0.0.1. В .env укажите:');
            $this->line('DB_HOST='.($this->ospanelMysqlHosts()[0] ?? 'mysql-8.0'));
            $this->line('DB_DATABASE=base_shop');
            $this->line('DB_USERNAME=root');
            $this->line('DB_PASSWORD=');
            $this->newLine();
            $this->comment('Модуль MySQL в панели должен быть запущен. Затем:');
            $this->line('php artisan migrate');
            $this->line('php artisan db:seed --class=Database\\Seeders\\RoleSeeder');
            $this->line('php artisan orchid:admin');
            $this->line('npm install && npm run build');

            return false;
        }
    }

    private function configureDatabase(): void
    {
        $username = (string) config('database.connections.mysql.username', 'root');
        $password = (string) config('database.connections.mysql.password', '');
        $database = (string) config('database.connections.mysql.database', 'base_shop');
        $port = (int) config('database.connections.mysql.port', 3306);
        $host = (string) config('database.connections.mysql.host', '127.0.0.1');

        if ($this->mysqlServerReachable($host, $username, $password, $port)) {
            $this->ensureSchemaExists($host, $database, $username, $password, $port);
            $this->applyDatabaseConfig($host, $database, $username, $password);

            return;
        }

        $ospanelHost = $this->firstReachableOspanelHost($username, $password, $port);

        if ($ospanelHost) {
            $this->warn('127.0.0.1:3306 недоступен. Для OSPanel ставлю DB_HOST='.$ospanelHost);
            $this->ensureSchemaExists($ospanelHost, $database, $username, $password, $port);
            $this->applyDatabaseConfig($ospanelHost, $database, $username, $password);

            return;
        }

        if ($this->option('no-interaction')) {
            $this->warn('MySQL недоступен. Проверьте DB_HOST в .env (для OSPanel: mysql-8.0).');

            return;
        }

        $this->warn('Не удалось подключиться к MySQL ('.$host.':'.$port.').');
        $host = text('Хост MySQL', default: $this->ospanelMysqlHosts()[0] ?? $host);
        $database = text('Имя базы', default: $database);
        $username = text('Пользователь MySQL', default: $username);
        $password = text('Пароль MySQL (пусто — Enter)', default: $password);

        $this->ensureSchemaExists($host, $database, $username, $password, $port);
        $this->applyDatabaseConfig($host, $database, $username, $password);
    }

    private function applyDatabaseConfig(string $host, string $database, string $username, string $password): void
    {
        $this->writeEnv('DB_HOST', $host);
        $this->writeEnv('DB_DATABASE', $database);
        $this->writeEnv('DB_USERNAME', $username);
        $this->writeEnv('DB_PASSWORD', $password);

        config([
            'database.connections.mysql.host' => $host,
            'database.connections.mysql.database' => $database,
            'database.connections.mysql.username' => $username,
            'database.connections.mysql.password' => $password,
        ]);

        DB::purge('mysql');
    }

    private function ensureSchemaExists(string $host, string $database, string $username, string $password, int $port): void
    {
        $name = str_replace('`', '', $database);
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d', $host, $port),
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $pdo->exec('CREATE DATABASE IF NOT EXISTS `'.$name.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    private function mysqlServerReachable(string $host, string $username, string $password, int $port): bool
    {
        try {
            new PDO(
                sprintf('mysql:host=%s;port=%d', $host, $port),
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]
            );

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return list<string>
     */
    private function ospanelMysqlHosts(): array
    {
        $modules = 'C:\\OSPanel\\modules';

        if (! is_dir($modules)) {
            return [];
        }

        $hosts = [];

        foreach (['MySQL-8.0', 'MySQL-8.2', 'MySQL-8.4', 'MySQL-5.7', 'MariaDB-10.11', 'MariaDB-10.6'] as $module) {
            if (is_dir($modules.DIRECTORY_SEPARATOR.$module)) {
                $hosts[] = strtolower($module);
            }
        }

        return $hosts;
    }

    private function firstReachableOspanelHost(string $username, string $password, int $port): ?string
    {
        foreach ($this->ospanelMysqlHosts() as $host) {
            if ($this->mysqlServerReachable($host, $username, $password, $port)) {
                return $host;
            }
        }

        return null;
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
