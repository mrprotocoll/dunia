<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'total_price',
        'shipping_price',
        'billing_address_id'
    ];
    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'total_price')->withTimestamps();
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    protected static function generateOrderNumber()
    {
        $orderId = 'ORD' . now()->format('YmdHis') . Str::random(5);
        while (static::where('order_number', $orderId)->exists()) {
            $orderId = 'ORD' . now()->format('YmdHis') . Str::random(5);
        }
        return $orderId;
    }
}
