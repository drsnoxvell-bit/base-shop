<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use AsSource;
    use Attachable;
    use Filterable;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'old_price',
        'quantity',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'sku' => Like::class,
        'category_id' => Where::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'price',
        'quantity',
        'sort',
        'updated_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('name');
    }

    public function gallery()
    {
        return $this->attachments('gallery');
    }

    public function cover(): ?Attachment
    {
        return $this->attachments('gallery')->first()
            ?? $this->attachments()->first();
    }

    public function coverUrl(): ?string
    {
        return $this->cover()?->url();
    }

    public function inStock(): bool
    {
        return $this->quantity > 0;
    }

    public function discountPercent(): ?int
    {
        if (! $this->old_price || (float) $this->old_price <= (float) $this->price) {
            return null;
        }

        return (int) round((1 - ((float) $this->price / (float) $this->old_price)) * 100);
    }

    public function savings(): ?float
    {
        if ($this->discountPercent() === null) {
            return null;
        }

        return (float) $this->old_price - (float) $this->price;
    }

    public function excerpt(int $limit = 110): string
    {
        $text = (string) preg_replace('/\s+/u', ' ', strip_tags((string) $this->description));

        $text = trim($text);

        return $text === '' ? '' : Str::limit($text, $limit);
    }

    public function stockStatus(): string
    {
        if ($this->quantity < 1) {
            return 'out';
        }

        if ($this->quantity <= 5) {
            return 'low';
        }

        return 'ok';
    }

    public function stockLabel(): string
    {
        return match ($this->stockStatus()) {
            'out' => 'Нет в наличии',
            'low' => 'Осталось '.$this->quantity.' шт.',
            default => 'В наличии · '.$this->quantity.' шт.',
        };
    }

    public function photosCount(): int
    {
        if ($this->relationLoaded('attachment')) {
            return $this->attachment->count();
        }

        return $this->attachments()->count();
    }
}
