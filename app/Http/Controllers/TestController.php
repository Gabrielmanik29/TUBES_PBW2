<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class TestController extends Controller
{
    /**
     * Test Midtrans connection with hardcoded values
     */
    public function testMidtrans()
    {
        try {
            // Hardcode Midtrans configuration
            Config::$serverKey = 'Mid-server-tKixyXh2zSYbgANesal-fK_o';
            Config::$isProduction = false;
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Disable SSL verification for testing
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ];

            // Hardcoded test parameters
            $orderId = 'TEST-' . time();
            $grossAmount = 10000;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => 'Test User',
                    'email' => 'test@example.com',
                ],
                'item_details' => [
                    [
                        'id' => 'TEST-ITEM-1',
                        'price' => (int) $grossAmount,
                        'quantity' => (int) 1,
                        'name' => 'Test Item - Midtrans Connection Test',
                    ]
                ],
            ];

            // Try to generate snap token
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'message' => 'Midtrans connection successful!',
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'server_key_used' => 'Mid-server-tKixyXh2zSYbgANesal-fK_o',
                'is_production' => false,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Midtrans connection failed: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_details' => [
                    'order_id' => $orderId ?? 'N/A',
                    'gross_amount' => $grossAmount ?? 'N/A',
                    'server_key_used' => 'Mid-server-tKixyXh2zSYbgANesal-fK_o',
                    'is_production' => false,
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                ],
                'stack_trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}