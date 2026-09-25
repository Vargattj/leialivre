{{--
Componente: Oferta de curso parceiro (Hotmart)
Divulgação temporária enquanto os guias de estudo próprios não estão
publicados. Remover (e as chamadas dele) quando os guias assumirem esses
espaços — ver resources/views/livros/index.blade.php e components/download-popup.blade.php.

Sorteia uma frase entre as variantes abaixo a cada renderização e manda o
texto sorteado pro Mixpanel — na visualização (quando `auto-view` está ativo,
o padrão) e no clique — via trackGuideEvent(), pra dar pra comparar qual
frase converte melhor. Cada frase nova entra só editando o array abaixo.

Uso:
  x-partner-offer placement="empty_search" :extra="['term' => $term ?? null]"

  Superfície controlada por JS (ex.: popup que só fica visível depois de um
  clique): auto-view=false evita contar visualização de algo que o usuário
  pode nunca ter chegado a ver; quem chama dispara
  trackGuideEvent('partner_offer_view', JSON.parse(el.dataset.tracking))
  manualmente no momento certo.
  x-partner-offer placement="download_popup" :extra="['book_id' => $book->id ?? null]" :auto-view="false"

  Atenção: nunca escrever "<x-partner-offer" (tag real) dentro deste bloco de
  comentário — o Blade compila tags de componente antes de descartar
  comentários, e um exemplo assim vira uma chamada recursiva real, estourando
  a memória em runtime (foi exatamente isso que quebrou aqui da primeira vez).
--}}

@props(['placement', 'extra' => [], 'autoView' => true])

@php
    $partnerOfferPhrases = [
        ['id' => 'v1', 'html' => 'Enquanto isso: fique forte em <strong>Português para concursos públicos</strong> — teoria + 1.300 questões comentadas.'],
        ['id' => 'v2', 'html' => 'Prova de concurso chegando? Domine <strong>Português</strong> com teoria direto ao ponto e 1.300 questões comentadas.'],
        ['id' => 'v3', 'html' => '<strong>1.300 questões comentadas</strong> de Português pra você não errar mais na prova do seu concurso.'],
        ['id' => 'v4', 'html' => 'Já pensou em passar num concurso público? Comece pelo <strong>Português</strong>: teoria + questões comentadas.'],
        ['id' => 'v5', 'html' => 'Redação, gramática, interpretação de texto — tudo o que cai na prova de <strong>Português</strong>, num só curso.'],
    ];

    $variant = $partnerOfferPhrases[array_rand($partnerOfferPhrases)];

    $trackingPayload = array_merge([
        'placement'    => $placement,
        'variant_id'   => $variant['id'],
        'variant_text' => strip_tags($variant['html']),
    ], $extra);
@endphp

<div id="partner-offer-{{ $placement }}" data-tracking='@json($trackingPayload)'>
    <p class="text-xs uppercase" style="color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 0.625rem;">
        Publicidade</p>
    <p class="text-sm" style="color: #4b5563; margin-bottom: 0.75rem;">{!! $variant['html'] !!}</p>
    <a href="https://go.hotmart.com/Q107762247G" target="_blank" rel="sponsored noopener"
        onclick="if (typeof trackGuideEvent === 'function') { trackGuideEvent('partner_offer_click', JSON.parse(this.closest('[data-tracking]').dataset.tracking)); }"
        class="inline-flex items-center justify-center gap-2 font-medium transition-all duration-200"
        style="padding: 0.75rem 1.5rem; background: #B8860B; color: white; border-radius: 0.75rem; cursor: pointer; text-decoration: none;">
        <i class="ri-graduation-cap-line"></i>Conhecer o curso
    </a>
</div>

@if ($autoView)
    <script>
        if (typeof trackGuideEvent === 'function') {
            trackGuideEvent('partner_offer_view', @json($trackingPayload));
        }
    </script>
@endif
