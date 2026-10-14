<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __construct(private PaymentService $payments)
    {
    }

    /**
     * Callback server-to-server dari Midtrans (PRD 4.6.4).
     */
    public function handle(Request $request)
    {
        $result = $this->payments->handleWebhook($request->all());

        $status = in_array($result, ['invalid_signature'], true) ? 403 : 200;

        return response()->json(['status' => $result], $status);
    }
}
