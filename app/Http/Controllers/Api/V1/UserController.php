<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\UserResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group User
 */
class UserController extends Controller
{

    /**
     * Display information about the currently logged-in user.
     *
     * @authenticated
     *
     * @response {
     *     "data": {
     *         "id": 1,
     *         "name": "John Doe",
     *         "email": "johndoe@example.com",
     *     }
     * }
     *
     * @return UserResource
     */
    public function show() : UserResource
    {
        return new UserResource(User::current());
    }

    /**
     * Change the authenticated user's password.
     *
     * @authenticated
     * @param Request $request The password change request.
     *
     * @bodyParam current_password string required The user's current password.
     * @bodyParam new_password string required The new password.
     * @bodyParam new_password_confirmation string required The confirmation of the new password.
     *
     * @response {
     *     "message": "Password changed successfully"
     * }
     * @response 401 {
     *     "error": "Current password is incorrect"
     * }
     * @response 422 {
     *     "error": "Validation failed."
     * }
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function changePassword(Request $request) : JsonResponse
    {
        $user = User::current();
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

        $user->update(['password' => Hash::make($request->input('new_password'))]);

        return response()->json(['message' => 'Password changed successfully'], 200);

    }

    /**
     * Retrieve a list of products purchased by the authenticated user.
     *
     * @group Products
     *
     * @authenticated
     *
     * @response \App\Http\Resources\ProductResource[]
     */
    public function products(): ResourceCollection
    {
        $user = User::current();
        $products = $user->products()->with(['categories', 'author']);
        return ProductResource::collection($products);
    }
}
