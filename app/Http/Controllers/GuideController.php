<?php

namespace App\Http\Controllers;

use App\Enums\AnalyticsEventType;
use App\Jobs\SyncLeadToBrevo;
use App\Models\AnalyticsEvent;
use App\Models\Lead;
use App\Models\StudyGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    private const ATTRIBUTION_SESSION_KEY = 'guide_attribution';
    private const LEAD_COOKIE = 'll_lead';

    // guide_landing_view — GET /guias/{slug}
    public function show(Request $request, string $slug)
    {
        $guide = StudyGuide::published()->where('slug', $slug)->with('book')->firstOrFail();

        $attribution = $this->captureAttribution($request);

        AnalyticsEvent::create([
            'event_type' => AnalyticsEventType::GuideLandingView->value,
            'book_id'    => $guide->book_id,
            'metadata'   => array_filter([
                'guide_id' => $guide->id,
                ...collect($attribution)->except('referrer')->all(),
            ]),
            'ip_address' => $request->ip(),
        ]);

        return view('guides.show', compact('guide'));
    }

    // guide_email_submit — POST /guias/{slug}/lead
    public function capture(Request $request, string $slug)
    {
        $guide = StudyGuide::published()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'email'   => 'required|email:filter|max:255',
            'consent' => 'required|accepted',
            'surface' => 'nullable|string|max:40',
        ]);

        $attribution = $request->session()->get(self::ATTRIBUTION_SESSION_KEY, []);

        $lead = Lead::updateOrCreate(
            ['email' => $validated['email']],
            [
                'source'           => 'guide',
                'book_id'          => $guide->book_id,
                'utm_source'       => $attribution['utm_source'] ?? null,
                'utm_medium'       => $attribution['utm_medium'] ?? null,
                'utm_campaign'     => $attribution['utm_campaign'] ?? null,
                'utm_content'      => $attribution['utm_content'] ?? null,
                'utm_term'         => $attribution['utm_term'] ?? null,
                'gclid'            => $attribution['gclid'] ?? null,
                'referrer'         => $attribution['referrer'] ?? $request->headers->get('referer'),
                'ip_address'       => $request->ip(),
                'consented_at'     => now(),
                'last_captured_at' => now(),
            ]
        );

        AnalyticsEvent::create([
            'event_type' => AnalyticsEventType::GuideEmailSubmit->value,
            'book_id'    => $guide->book_id,
            'lead_id'    => $lead->id,
            'metadata'   => [
                'lead_id' => $lead->id,
                'book_id' => $guide->book_id,
                'surface' => $validated['surface'] ?? 'popup',
            ],
            'ip_address' => $request->ip(),
        ]);

        // Não bloqueia a resposta: se o Brevo estiver fora, o usuário ainda
        // recebe o link de download e o lead já está gravado.
        SyncLeadToBrevo::dispatch($lead);

        $downloadUrl = route('guias.download', [
            'slug'  => $guide->slug,
            'token' => $lead->public_id,
        ]);

        return response()->json([
            'success'      => true,
            'download_url' => $downloadUrl,
        ])->withCookie(cookie(self::LEAD_COOKIE, $lead->public_id, 60 * 24 * 365));
    }

    /**
     * guide_download — GET /guias/{slug}/baixar/{token?}
     *
     * Link aberto, sem assinatura: o guia grátis é a isca do produto pago, e
     * link compartilhado que funciona joga a favor. O token só atribui o
     * download a um lead; sem ele (ou com um token que não resolve) o PDF é
     * entregue do mesmo jeito, apenas sem lead_id no evento.
     */
    public function download(Request $request, string $slug, ?string $token = null)
    {
        $guide = StudyGuide::published()->where('slug', $slug)->firstOrFail();

        if (! $guide->hasFile()) {
            abort(404, 'Arquivo do guia ainda não configurado.');
        }

        // public_id é uuid no banco: token fora do formato faria o Postgres
        // estourar em vez de simplesmente não encontrar o lead.
        $lead = $token && Str::isUuid($token)
            ? Lead::where('public_id', $token)->first()
            : null;

        AnalyticsEvent::create([
            'event_type' => AnalyticsEventType::GuideDownload->value,
            'book_id'    => $guide->book_id,
            'lead_id'    => $lead?->id,
            'metadata'   => array_filter([
                'lead_id'  => $lead?->id,
                'guide_id' => $guide->id,
            ]),
            'ip_address' => $request->ip(),
        ]);

        $response = Storage::disk(StudyGuide::disk())
            ->download($guide->free_pdf_path, Str::slug($guide->title) . '.pdf');

        // Quem chega pelo link do e-mail nunca passou pelo site neste
        // dispositivo: aproveita para plantar o cookie, senão o checkout_start
        // depois sai sem lead. Storage::download() devolve um StreamedResponse
        // do Symfony, que não tem withCookie() — vai pelo headers.
        if ($lead) {
            $response->headers->setCookie(
                cookie(self::LEAD_COOKIE, $lead->public_id, 60 * 24 * 365)
            );
        }

        return $response;
    }

    /**
     * Captura utm_source, utm_medium, utm_campaign, utm_content, utm_term,
     * gclid e referrer da query string na sessão (primeira etapa da cadeia
     * de atribuição) e devolve o que está acumulado até agora.
     */
    private function captureAttribution(Request $request): array
    {
        $incoming = array_filter([
            'utm_source'   => $request->query('utm_source'),
            'utm_medium'   => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_content'  => $request->query('utm_content'),
            'utm_term'     => $request->query('utm_term'),
            'gclid'        => $request->query('gclid'),
            'referrer'     => $request->headers->get('referer'),
        ]);

        if (! empty($incoming)) {
            $request->session()->put(self::ATTRIBUTION_SESSION_KEY, $incoming);
        }

        return $request->session()->get(self::ATTRIBUTION_SESSION_KEY, []);
    }
}
