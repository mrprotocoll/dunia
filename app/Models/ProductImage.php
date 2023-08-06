<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductImage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name'
    ];

    public function product(): HasOne {
        return $this->hasOne(Product::class);
    }
}
