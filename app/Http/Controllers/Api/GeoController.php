<?php

namespace App\Http\Controllers\Api;

use App\Helpers\StatusEnum;
use App\Http\Controllers\Controller;
use App\Mail\AdminMails;
use App\Mail\OrderReceived;
use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * @group Countries states cities
 */
class GeoController extends Controller
{
    //
    /**
     * Get Countries
     * Retrieve a list of all countries.
     *
     * @response [
     *     {
     *         "id": 1,
     *         "name": "Country Name",
     *     },
     *     {
     *          "id": 1,
     *          "name": "Country Name",
     *      }
     * ]
     *
     * @return JsonResponse
     */
    public function countries(): JsonResponse
    {
        $countries = Country::all();
        return response()->json($countries);
    }

    /**
     * Get states
     * Retrieve a list of states within a specific country.
     *
     * @urlParam country required The ID of the country.
     *
     * @response [
     *     {
     *         "id": 1,
     *         "name": "State Name",
     *     },
     *     {
     *          "id": 1,
     *          "name": "State Name",
     *      }
     * ]
     *
     * @param Country $country
     * @return JsonResponse
     */
    public function states(Country $country): JsonResponse {
        $states = $country->states;
        return response()->json($states);
    }

    /**
     * Get cities
     * Retrieve a list of cities within a specific state.
     *
     * @urlParam state required The ID of the state.
     *
     * @response [
     *      {
     *          "id": 1,
     *          "name": "City Name",
     *      },
     *      {
     *           "id": 1,
     *           "name": "City Name",
     *       }
     * ]
     *
     * @param State $state
     * @return JsonResponse
     */
    public function cities(State $state): JsonResponse {
        $cities = $state->cities;
        return response()->json($cities);
    }
}
