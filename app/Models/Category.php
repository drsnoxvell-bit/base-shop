<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Category extends Model
{
    use AsSource;
    use Filterable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'slug' => Like::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'sort',
        'is_active',
        'updated_at',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('name');
    }
}
