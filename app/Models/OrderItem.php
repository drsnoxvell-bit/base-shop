<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class OrderItem extends Model
{
    use AsSource;

    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'price',
        'qty',
        'sum',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'qty' => 'integer',
        'sum' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
