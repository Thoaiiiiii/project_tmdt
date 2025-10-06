<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaypalController extends Controller
{
    // Hàm tạo order
    public function createOrder(Request $request)
    {
        // 🔸 Bước 1: Lấy access token
        $clientId = env('PAYPAL_SANDBOX_CLIENT_ID');
        $clientSecret = env('PAYPAL_SANDBOX_CLIENT_SECRET');

        $response = Http::asForm()->withBasicAuth($clientId, $clientSecret)
            ->post('https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        $accessToken = $response->json()['access_token'];

        // 🔸 Bước 2: Tạo order
        $orderResponse = Http::withToken($accessToken)
            ->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "100.00"
                    ],
                    "reference_id" => "d9f80740-38f0-11e8-b467-0ed5f89f718b"
                ]],
                "payment_source" => [
                    "paypal" => [
                        "experience_context" => [
                            "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                            "payment_method_selected" => "PAYPAL",
                            "brand_name" => "EXAMPLE INC",
                            "locale" => "en-US",
                            "landing_page" => "LOGIN",
                            "shipping_preference" => "GET_FROM_FILE",
                            "user_action" => "PAY_NOW",
                            "return_url" => "https://example.com/returnUrl",
                            "cancel_url" => "https://example.com/cancelUrl"
                        ]
                    ]
                ]
            ]);

        // 🔸 Trả JSON response
        return response()->json($orderResponse->json());
    }

    public function captureOrder(Request $request, $orderId)
    {
        $clientId = env('PAYPAL_SANDBOX_CLIENT_ID');
        $clientSecret = env('PAYPAL_SANDBOX_CLIENT_SECRET');

        // Lấy access token
        $response = Http::asForm()->withBasicAuth($clientId, $clientSecret)
            ->post('https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        $accessToken = $response->json()['access_token'];
        $orderId = $request->input('orderID');

        // Capture order
        $captureResponse = Http::withToken($accessToken)
            ->post("https://api-m.sandbox.paypal.com/v2/checkout/orders/{$orderId}/capture");

        return response()->json($captureResponse->json());
    }
}
