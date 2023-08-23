<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OrderRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Stripe\Event;
use Stripe\StripeClient;

/**
 * @group Order Management
 *
 * Endpoint to manage customers orders
 */
class OrderController extends Controller
{
    /**
     * Get All orders
     * Retrieve a paginated list of orders.
     *
     * @authenticated
     *
     * @response {
     *     "data": [
     *         {
     *             "id": 1,
     *             "user": {
     *                 "id": 2,
     *                 "name": "John Doe",
     *                 "email": "johndoe@example.com"
     *             },
     *             "cart": [...],
     *             "createdAt": "2023-08-11T12:34:56Z",
     *             "updatedAt": "2023-08-11T12:34:56Z"
     *         },
     *         ...
     *     ],
     *     "links": {
     *         "first": "...",
     *         "last": "...",
     *         "prev": null,
     *         "next": "..."
     *     },
     *     "meta": {
     *         "current_page": 1,
     *         "from": 1,
     *         "last_page": 3,
     *         "path": "...",
     *         "per_page": 10,
     *         "to": 10,
     *         "total": 30
     *     }
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse | ResourceCollection
     */
    public function index() {
        try {
            $orders = Order::with(['user:id,name,email', 'products'])->paginate();
            return OrderResource::collection($orders);
        }
        catch (\Exception $exception) {
            return response()->json('Oops something went wrong', 500);
        }
    }


    /**
     * Get order by ID
     * Retrieve detailed information about a specific order.
     *
     * @authenticated
     * @param \App\Models\Order $order The order to retrieve.
     *
     * @response {
     *     "data": {
     *         "id": 1,
     *         "user": {
     *             "id": 2,
     *             "name": "John Doe",
     *             "email": "johndoe@example.com"
     *         },
     *         "products": [...],
     *         "createdAt": "2023-08-11T12:34:56Z",
     *         "updatedAt": "2023-08-11T12:34:56Z"
     *     }
     * }
     * @response 404 {
     *     "message": "Order not found"
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        //
        try {
            // Eager load the user and products relationships during the query
            $order = Order::with(['user', 'products'])->find($order->id);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            return new OrderResource($order);
        }
        catch (\Exception $exception) {
            return response()->json('Oops something went wrong', 500);
        }
    }

}
