<?php

namespace App\Http\Controllers;

use App\Models\PaymentWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Endpoint reservado (seção 03/08-F do spec de instrumentação).
     *
     * Por ora só valida a assinatura (HMAC genérico via
     * PAYMENT_WEBHOOK_SECRET) e grava o payload cru. Resolver o lead pelo
     * public_id e gravar o evento `purchase` fica para quando a plataforma
     * de pagamento for escolhida e o parser do payload dela for escrito.
     */
    public function handle(Request $request)
    {
        $signatureValid = $this->hasValidSignature($request);

        PaymentWebhookLog::create([
            'signature_valid' => $signatureValid,
            'headers'         => $request->headers->all(),
            'payload'         => $request->getContent(),
        ]);

        if (! $signatureValid) {
            Log::warning('Postback de pagamento recebido com assinatura inválida.');

            return response()->json(['success' => false], 401);
        }

        return response()->json(['success' => true]);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = config('services.payment.webhook_secret');

        if (empty($secret)) {
            return false;
        }

        $signature = (string) $request->header('X-Webhook-Signature', '');
        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}
