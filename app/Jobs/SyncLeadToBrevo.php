<?php

namespace App\Jobs;

use App\Enums\AnalyticsEventType;
use App\Models\AnalyticsEvent;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncLeadToBrevo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public Lead $lead)
    {
    }

    public function handle(): void
    {
        $key = config('services.brevo.key');
        $listId = config('services.brevo.list_id');

        if (empty($key)) {
            Log::info('Brevo não configurado (BREVO_API_KEY ausente) — pulando sincronização.', [
                'lead_id' => $this->lead->id,
            ]);

            return;
        }

        $response = Http::withHeaders([
            'api-key'      => $key,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/contacts', [
            'email'         => $this->lead->email,
            'listIds'       => $listId ? [(int) $listId] : [],
            'updateEnabled' => true,
            'attributes'    => array_filter([
                'OBRA'         => $this->lead->book?->title,
                'ORIGEM'       => $this->lead->source,
                'UTM_CAMPAIGN' => $this->lead->utm_campaign,
                'DATA_CAPTURA' => $this->lead->last_captured_at?->toDateString(),
                'PUBLIC_ID'    => $this->lead->public_id,
                // URL pronta em vez de pedaços: a automação do Brevo é uma só
                // para todas as obras e não teria como saber o slug do guia.
                // No e-mail basta {{ contact.GUIA_URL }}.
                'GUIA_URL'     => $this->guideDownloadUrl(),
            ]),
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                "Falha ao sincronizar lead {$this->lead->id} com o Brevo: {$response->status()} {$response->body()}"
            );
        }

        // Contato novo: Brevo devolve {"id": ...}. Contato já existente com
        // updateEnabled=true costuma devolver 204 sem corpo — nesse caso o
        // brevo_contact_id simplesmente não é preenchido nesta chamada.
        $contactId = $response->json('id');
        if ($contactId) {
            $this->lead->update(['brevo_contact_id' => (string) $contactId]);
        }

        // guide_delivered = a API do Brevo aceitou o contato (o e-mail saiu do
        // nosso lado e entrou na automação). Não é confirmação de abertura/
        // entrega real na caixa do destinatário — isso exigiria um webhook de
        // eventos do Brevo, que não existe ainda. Mas já separa "oferta ruim"
        // (sem guide_email_submit) de "e-mail não chegou" (submit sem isto).
        if ($this->lead->source === 'guide') {
            AnalyticsEvent::create([
                'event_type' => AnalyticsEventType::GuideDelivered->value,
                'book_id'    => $this->lead->book_id,
                'lead_id'    => $this->lead->id,
                'metadata'   => array_filter([
                    'lead_id'  => $this->lead->id,
                    'guide_id' => $this->lead->book?->guide?->id,
                ]),
                'ip_address' => $this->lead->ip_address,
            ]);
        }
    }

    /**
     * Link de download do guia já com o token do lead, para o e-mail atribuir
     * o download a quem clicou. Null quando o livro ainda não tem guia
     * publicado — nesse caso o atributo simplesmente não é enviado.
     */
    private function guideDownloadUrl(): ?string
    {
        $guide = $this->lead->book?->guide;

        if (! $guide || ! $guide->is_published) {
            return null;
        }

        return route('guias.download', [
            'slug'  => $guide->slug,
            'token' => $this->lead->public_id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Falha definitiva ao sincronizar lead com o Brevo', [
            'lead_id' => $this->lead->id,
            'error'   => $exception->getMessage(),
        ]);
    }
}
