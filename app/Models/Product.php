<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
