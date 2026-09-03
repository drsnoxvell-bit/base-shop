<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\File as OrchidFile;

return new class extends Migration
{
    public function up(): void
    {
        Setting::query()->updateOrCreate(['key' => 'site'], [
            'value' => [
                'name' => 'Base Shop',
                'description' => 'Простой интернет-магазин на Laravel и Orchid',
                'phone' => '+7 (900) 000-00-00',
                'email' => 'shop@example.test',
                'address' => 'Москва, ул. Примерная, 1',
            ],
        ]);

        Setting::query()->updateOrCreate(['key' => 'mail'], [
            'value' => [
                'mailer' => 'log',
                'host' => '127.0.0.1',
                'port' => '2525',
                'username' => '',
                'password' => '',
                'encryption' => '',
                'from_address' => 'shop@example.test',
                'from_name' => 'Base Shop',
            ],
        ]);

        $categories = [
            [
                'name' => 'Кофе и чай',
                'slug' => 'coffee-tea',
                'description' => 'Зёрна, молотый кофе и листовой чай.',
                'sort' => 1,
            ],
            [
                'name' => 'Посуда',
                'slug' => 'tableware',
                'description' => 'Кружки, френч-прессы и аксессуары для сервировки.',
                'sort' => 2,
            ],
            [
                'name' => 'Сладости',
                'slug' => 'sweets',
                'description' => 'Шоколад, мёд и добавки к напиткам.',
                'sort' => 3,
            ],
            [
                'name' => 'Аксессуары',
                'slug' => 'accessories',
                'description' => 'Кофемолки, сиропы и мелочи для кухни.',
                'sort' => 4,
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(['slug' => $category['slug']], $category + ['is_active' => true]);
        }

        $ids = Category::query()->pluck('id', 'slug');

        $products = [
            ['sku' => 'CF-ETH-1', 'slug' => 'ethiopia-beans', 'name' => 'Кофе в зёрнах Ethiopia', 'category' => 'coffee-tea', 'price' => 890, 'old_price' => 1090, 'qty' => 24, 'images' => 3, 'color' => [140, 74, 42], 'text' => "Кофе\nEthiopia"],
            ['sku' => 'TEA-SEN-1', 'slug' => 'sencha-green', 'name' => 'Зелёный чай сенча', 'category' => 'coffee-tea', 'price' => 520, 'old_price' => null, 'qty' => 18, 'images' => 2, 'color' => [46, 110, 72], 'text' => "Чай\nсенча"],
            ['sku' => 'CUP-CER-1', 'slug' => 'ceramic-mug', 'name' => 'Керамическая кружка', 'category' => 'tableware', 'price' => 650, 'old_price' => 790, 'qty' => 30, 'images' => 4, 'color' => [176, 92, 80], 'text' => "Кружка"],
            ['sku' => 'FP-800', 'slug' => 'french-press', 'name' => 'Френч-пресс 800 мл', 'category' => 'tableware', 'price' => 1490, 'old_price' => null, 'qty' => 12, 'images' => 2, 'color' => [70, 90, 110], 'text' => "Френч-\nпресс"],
            ['sku' => 'CH-70', 'slug' => 'dark-chocolate', 'name' => 'Горький шоколад 70%', 'category' => 'sweets', 'price' => 280, 'old_price' => null, 'qty' => 40, 'images' => 1, 'color' => [92, 52, 40], 'text' => "Шоколад"],
            ['sku' => 'HN-FL', 'slug' => 'flower-honey', 'name' => 'Мёд цветочный 500 г', 'category' => 'sweets', 'price' => 740, 'old_price' => 820, 'qty' => 16, 'images' => 3, 'color' => [196, 140, 40], 'text' => "Мёд"],
            ['sku' => 'GR-HND', 'slug' => 'hand-grinder', 'name' => 'Кофемолка ручная', 'category' => 'accessories', 'price' => 2390, 'old_price' => 2690, 'qty' => 8, 'images' => 2, 'color' => [90, 90, 96], 'text' => "Кофемолка"],
            ['sku' => 'SYR-VN', 'slug' => 'vanilla-syrup', 'name' => 'Сироп ванильный', 'category' => 'accessories', 'price' => 430, 'old_price' => null, 'qty' => 22, 'images' => 5, 'color' => [214, 176, 120], 'text' => "Сироп"],
        ];

        foreach ($products as $index => $item) {
            $product = Product::query()->updateOrCreate(['slug' => $item['slug']], [
                'category_id' => $ids[$item['category']],
                'name' => $item['name'],
                'sku' => $item['sku'],
                'description' => $item['name'].' — тестовый товар для демонстрации каталога, корзины и галереи.',
                'price' => $item['price'],
                'old_price' => $item['old_price'],
                'quantity' => $item['qty'],
                'is_active' => true,
                'sort' => $index + 1,
            ]);

            $attachmentIds = [];

            for ($i = 1; $i <= $item['images']; $i++) {
                $attachmentIds[] = $this->makeAttachment(
                    $item['slug'].'-'.$i,
                    $item['color'],
                    $item['text'].($item['images'] > 1 ? "\n".$i : ''),
                )->id;
            }

            $product->attachment()->sync($attachmentIds);
        }
    }

    public function down(): void
    {
        Product::query()->whereIn('slug', [
            'ethiopia-beans', 'sencha-green', 'ceramic-mug', 'french-press',
            'dark-chocolate', 'flower-honey', 'hand-grinder', 'vanilla-syrup',
        ])->each(function (Product $product) {
            $product->attachment()->detach();
            $product->delete();
        });

        Category::query()->whereIn('slug', ['coffee-tea', 'tableware', 'sweets', 'accessories'])->delete();
        Setting::query()->whereIn('key', ['site', 'mail'])->delete();
    }

    private function makeAttachment(string $basename, array $rgb, string $label)
    {
        $directory = database_path('data/images');
        File::ensureDirectoryExists($directory);
        $path = $directory.DIRECTORY_SEPARATOR.$basename.'.jpg';

        if (! is_file($path)) {
            $this->drawJpeg($path, $rgb, $label);
        }

        $uploaded = new UploadedFile($path, $basename.'.jpg', 'image/jpeg', null, true);

        return (new OrchidFile($uploaded, 'public', 'gallery'))->load();
    }

    private function drawJpeg(string $path, array $rgb, string $label): void
    {
        $image = imagecreatetruecolor(900, 700);
        $bg = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        $light = imagecolorallocate($image, min(255, $rgb[0] + 40), min(255, $rgb[1] + 40), min(255, $rgb[2] + 40));
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, 0, 0, 900, 700, $bg);
        imagefilledellipse($image, 680, 160, 320, 320, $light);
        imagestring($image, 5, 80, 300, str_replace("\n", ' / ', $label), $white);
        imagejpeg($image, $path, 86);
        imagedestroy($image);
    }
};
