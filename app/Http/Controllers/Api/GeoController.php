<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    //
    public function countries(): JsonResponse
    {
        $countries = Country::all();
        return response()->json($countries);
    }

    public function states(): JsonResponse
    {
        $states = State::all();
        return response()->json($states);
    }

    public function cities(): JsonResponse
    {
        $cities = City::all();
        return response()->json($cities);
    }
}
