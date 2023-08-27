<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id', 'state_id', 'name'
    ];

    protected $hidden = [
        'deleted_at', 'updated_at', 'created_at', 'status'
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
