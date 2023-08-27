<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OrderRequest;
use App\Http\Resources\V1\OrderResource;
use App\Mail\AdminMails;
use App\Mail\OrderReceived;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Event;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * @group Order Management
 *
 * Endpoint to manage customers orders
 */
class OrderController extends Controller
{

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $user = User::current();
        DB::transaction(function () use ($user, $request) {
            $shipping = $request['shipping'];
            $order = $user->orders()->create([
                'total_price' => $request['total'],
                'shipping_price' => $shipping['price'] ?? 0,
                'billing_address_id' => $shipping['billing_address_id'],
                'session_id' => $request['session_id']
            ]);

            foreach($request['cart'] as $productData){
                $product = Product::findOrFail($productData['product_id']);
                $quantity = intval($productData['quantity']);
                $total_price = $quantity * floatval($product->price);

                $order->products()->attach($product, ['quantity' => $quantity, 'total_price' => $total_price]);
            }
        });
    }

    /**
     *
     * Get customer orders
     * Retrieve a paginated list of orders for the currently authenticated customer.
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
            $user = User::current();
            $orders = $user->orders()->with(['user:id,name,email', 'products'])->paginate();
            return OrderResource::collection($orders);
        }
        catch (\Exception $exception) {
            return response()->json('Oops something went wrong', 500);
        }
    }

    /**
     * Get Customer order by order ID
     * Retrieve detailed information about a specific order for the currently authenticated user.
     *
     * @authenticated
     *
     * @param Order $order The order to retrieve.
     *
     * @response {
     *     "data": {
     *         "id": 1,
     *         "user": {
     *             "id": 2,
     *             "name": "John Doe",
     *             "email": "johndoe@example.com"
     *         },
     *         "cart": [...],
     *     }
     * }
     * @response 404 {
     *     "message": "Order not found"
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse | OrderResource
     */
    public function show(Order $order)
    {
        try {
            $user = User::current();
            // Eager load the user and products relationships during the query
            $order = $user->orders()->with(['user', 'products'])->find($order->id);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            return new OrderResource($order);
        }
        catch (\Exception $exception) {
            return response()->json('Oops something went wrong', 500);
        }
    }

    /**
     * Process the checkout for placing an order.
     *
     * @authenticated
     * @param OrderRequest $request The order request.
     *
     * @bodyParam cart array required The array of products in the cart.
     * @bodyParam cart[].product_id int required The ID of the product in the cart.
     * @bodyParam cart[].quantity int required The quantity of the product in the cart.
     * @bodyParam shipping.billing_address_id string shipping address_id if shipping is involved.
     * @bodyParam shipping.price float required The shipping price if shipping is involved.
     * @bodyParam success_url string required The URL to redirect to on successful payment.
     * @bodyParam cancel_url string required The URL to redirect to on payment cancellation.
     *
     * @response {
     *     "url": "https://checkout.stripe.com/session/...",
     *     "message": "Checkout initiated successfully"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(OrderRequest $request) {
        $request->validated();
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $line_items = [];
        $shipping = $request['shipping'];
        $request['total'] = 0;
        foreach ($request['cart'] as $item) {
            $product = Product::find($item['product_id']);
            // Add print_price to product price if shipping is involved
            $price = $shipping
                ? floatval($product->price) + floatval($product->print_price)
                : floatval($product->price);

            // reset total price of all ordered products in cart
            $request['total'] += $price * intval($item['quantity']);

            // add product data to stripe items
            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $price * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }

        if(count($request['shipping']) > 1) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Shipping',
                    ],
                    'unit_amount' => $shipping['price'] * 100,
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $request->success_url."?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => $request->cancel_url,
        ]);

        if($checkout_session->url) {
            $request['session_id'] = $checkout_session->id;
            // add a new order
            $this->store($request);
        }

        return response()->json(['url' => $checkout_session->url]);
    }

    public function webhook() {
        // Set secret key.
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = env('WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        function fulfill_order($session) {
            $order = Order::where('session_id', $session->id);
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

        }

        function email_customer_about_failed_payment($session) {
            // TODO fill me in
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // A delayed notification payment will have an `unpaid` status, as
                if ($session->payment_status == 'paid') {
                    // Fulfill the purchase
                    fulfill_order($session);
                }

                break;

            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;

                // Fulfill the purchase
                fulfill_order($session);

                break;

            case 'checkout.session.async_payment_failed':
                $session = $event->data->object;
                // TODO: send email to customer of failed transaction
                // Send an email to the customer asking them to retry their order
                email_customer_about_failed_payment($session);
                break;
        }

        response('',200);
    }

}
