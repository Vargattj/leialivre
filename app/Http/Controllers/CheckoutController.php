<?php

namespace App\Http\Controllers;

use App\Enums\AnalyticsEventType;
use App\Models\AnalyticsEvent;
use App\Models\Lead;
use App\Models\StudyGuide;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    // checkout_start — GET /checkout/{slug}. Link estável impresso nos PDFs:
    // nunca aponta direto para a plataforma de pagamento, para que trocar de
    // plataforma ou de preço não exija reemitir os arquivos.
    public function redirect(Request $request, string $slug)
    {
        $guide = StudyGuide::published()->where('slug', $slug)->firstOrFail();

        $lead = null;
        $publicId = $request->cookie('ll_lead');
        if ($publicId) {
            $lead = Lead::where('public_id', $publicId)->first();
        }

        AnalyticsEvent::create([
            'event_type' => AnalyticsEventType::CheckoutStart->value,
            'book_id'    => $guide->book_id,
            'lead_id'    => $lead?->id,
            'metadata'   => array_filter([
                'lead_id'  => $lead?->id,
                'guide_id' => $guide->id,
            ]),
            'ip_address' => $request->ip(),
        ]);

        if (! $guide->checkout_url) {
            return redirect()->route('guias.show', $guide->slug);
        }

        $target = $guide->checkout_url;

        if ($lead) {
            // Nome do parâmetro é um placeholder genérico — trocar pelo campo
            // de rastreio real da plataforma de pagamento quando ela entrar.
            $separator = str_contains($target, '?') ? '&' : '?';
            $target .= $separator . 'lead=' . urlencode($lead->public_id);
        }

        return redirect()->away($target);
    }
}
