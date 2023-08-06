<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name'
    ];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }

}
