<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 *
 * Endpoint to manage user authentication
 */
class LogoutController extends Controller
{
    /**
     * Logout.
     */
    public function __invoke(Request $request)
    {
        //
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'logged out successfuly'], 204);
    }
}
