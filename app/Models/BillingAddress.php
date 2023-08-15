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

    protected $fillable = ['address'];

    public function user(): BelongsTo {
        return $this->BelongsTo(User::class);
    }

    public function country(): BelongsTo {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo {
        return $this->BelongsTo(State::class);
    }

    public function city(): BelongsTo {
        return $this->BelongsTo(City::class);
    }
}
