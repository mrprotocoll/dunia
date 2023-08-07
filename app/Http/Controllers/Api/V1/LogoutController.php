<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'logged out successfuly'], 204);
    }
}
