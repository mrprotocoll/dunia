<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUuids;

    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function categories(): BelongsToMany {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function images(): HasMany {
        return $this->hasMany(ProductImage::class);
    }

    public function author(): BelongsTo {
        return $this->belongsTo(Author::class);
    }

    public function addImages(Request $request): void {

        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . Str::slug($image->getClientOriginalName(), '_');

            // Store the image in the 'public' disk (storage/app/public)
            Storage::disk('public')->put($imageName, file_get_contents($image));

            // Save the image file name in the database
            $this->images()->create(['name' => $imageName]);
        }
    }
}
