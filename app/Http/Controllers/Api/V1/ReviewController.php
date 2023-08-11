<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ReviewRequest;
use App\Http\Resources\V1\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    //
    public function store(Product $product, ReviewRequest $request): ReviewResource {
        $user = Auth::user();
        $request = $request->validated();
        $review = new Review();
        $review->user_id = $user->id;
        $review->product_id = $product->id;
        $review->comment = $request['comment'];
        $review->rating = $request['rating'];
        $review->save();

        return new ReviewResource($review);
    }
}
