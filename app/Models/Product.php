<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    public function tags(): BelongsToMany {
        return $this->BelongsToMany(Tag::class);
    }

    public function categories(): BelongsToMany {
        return $this->BelongsToMany(Category::class);
    }

    public function images(): HasMany {
        return $this->hasMany(ProductImages::class);
    }
}
