<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Order extends Model
{
    use AsSource;
    use Filterable;

    protected $fillable = [
        'number',
        'name',
        'phone',
        'email',
        'address',
        'comment',
        'status',
        'total',
        'stock_taken',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'total' => 'decimal:2',
        'stock_taken' => 'boolean',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'number' => Like::class,
        'name' => Like::class,
        'phone' => Like::class,
        'status' => Where::class,
    ];

    protected $allowedSorts = [
        'id',
        'number',
        'total',
        'status',
        'created_at',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
