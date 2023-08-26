<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ReviewRequest;
use App\Http\Resources\V1\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

/**
 * @group Product Review
 *
 * customer review on a product
 */
class ReviewController extends Controller
{
    /**
     * Create a new review for a product.
     *
     * @authenticated
     *
     * @param Product $product The product to which the review is being added.
     * @param ReviewRequest $request The review creation request.
     *
     * @bodyParam comment string required The comment for the review.
     * @bodyParam rating integer required The rating for the review (0-5).
     *
     * @response {
     *     "data": {
     *          "id": "99ddc991-b5ea-4985-9733-ed9a4b459c0e",
     *          "user": {
     *              "id": "99cf69a0-2af8-4baf-bb7a-69fc531e9660",
     *              "name": "admin",
     *              "email": "admin@gmail.com"
     *          },
     *          "comment": "lorem ipsum",
     *          "rating": "4",
     *          "createdAt": "2023-08-11T18:28:05.000000Z"
     *      }
     * }
     *
     * @return ReviewResource
     */
    public function store(Product $product, ReviewRequest $request): ReviewResource {
        $user = Auth::user();
        $request = $request->validated();
        $review = new Review();
        $review->user_id = $user->id;
        $review->product_id = $product->id;
        $review->comment = $request['comment'];
        $review->summary = $request['summary'];
        $review->rating = $request['rating'];
        $review->save();

        return new ReviewResource($review);
    }
}
