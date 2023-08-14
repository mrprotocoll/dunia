<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BillingAddress extends Model
{
    use HasFactory, HasUuids;

    public function countries(): HasMany {
        return $this->hasMany(Country::class);
    }

    public function states(): HasMany {
        return $this->hasMany(State::class);
    }

    public function cities(): HasMany {
        return $this->hasMany(City::class);
    }
}
