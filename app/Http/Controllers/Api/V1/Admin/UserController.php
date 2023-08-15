<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AuthorResource;
use App\Models\Author;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group User
 */
class UserController extends Controller
{

    /**
     * Display paginated list of customers
     *
     * @authenticated Admin Authentication
     * @apiResourceModel App\Models\User
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::where('role', 'customer')->paginate();
        return response()->json(['data' => $users]);
    }

    /**
     * Display a user by ID.
     * @authenticated Admin Authentication
     *
     * @apiResource JsonResource
     * @apiResourceModel \App\Models\User
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user) : JsonResponse
    {
        return response()->json(['data' => $user]);
    }

}
