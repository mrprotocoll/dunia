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
        $order = Order::find('99ee5ee4-e6e0-4202-b79f-18b2fdaf4068');
        $status = $order->shipping_price < 1 ? StatusEnum::SUCCESS : StatusEnum::AWAITING_SHIPMENT;
        $order->status = $status;
        // TODO: Add product to user products
        foreach ($order->products as $product) {
            $product->attach($order->user);
        }

        if($order->save()) {
            // TODO: Send email to customer
            Mail::to($order->user)->send(new OrderReceived($order));

            // TODO: Send email to admin of a new order
            Mail::send(new AdminMails('newOrder', 'New Order on Dunia'));
        }
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
