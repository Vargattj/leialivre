{{--
Componente: Barra de progresso do mês (meta = custo mensal de manter o site)
O valor arrecadado é informado à mão em config/donation.php (ver 'goal') e volta
a zero sozinho quando o mês muda. Não renderiza nada sem custo preenchido.

Uso: x-donation-progress. compact: versão enxuta do popup. hide-when-empty: some enquanto nada foi arrecadado no mês (no popup não vale mostrar 0%).
--}}

@props(['compact' => false, 'hideWhenEmpty' => false])

@php
    $progress = \App\Support\Donation::progress();
@endphp

@if ($progress && ! ($hideWhenEmpty && $progress['raised'] <= 0))
    <div class="text-left" style="{{ $compact ? 'margin-bottom: 1rem;' : '' }}">
        <div class="flex justify-between items-baseline" style="margin-bottom: 0.375rem; gap: 0.5rem;">
            <span class="{{ $compact ? 'text-xs' : 'text-sm' }}" style="color: #333333; font-weight: 600;">
                {{ \App\Support\Donation::money($progress['raised']) }} de {{ \App\Support\Donation::money($progress['goal']) }} neste mês
            </span>
            <span class="text-xs" style="color: #6b7280;">{{ $progress['percent'] }}%</span>
        </div>

        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress['percent'] }}"
            aria-label="Progresso da meta do mês"
            style="height: {{ $compact ? '0.5rem' : '0.75rem' }}; background: #e5e7eb; border-radius: 999px; overflow: hidden;">
            <div style="height: 100%; width: {{ $progress['percent'] }}%; background: linear-gradient(to right, #004D40, #00695C); border-radius: 999px; transition: width 0.4s;"></div>
        </div>

        <p class="text-xs" style="color: #6b7280; margin-top: 0.375rem;">
            @if ($progress['reached'])
                Meta do mês atingida. Obrigado!
            @else
                Meta do mês: cobrir os custos de manter o site no ar.
            @endif
            @if ($progress['updated_at'])
                Atualizado em {{ $progress['updated_at'] }}.
            @endif
        </p>
    </div>
@endif
