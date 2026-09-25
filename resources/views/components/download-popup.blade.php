{{--
Componente: Download Popup
Uso: <x-download-popup :book="$book" />

Funções JS disponíveis globalmente após incluir este componente:
- handleDownload(btn) → chamar no onclick do botão de download
- closeDownloadPopup() → fechar o popup manualmente

O download do livro nunca é bloqueado por este popup: ele dispara imediatamente
e o popup abre em paralelo, mostrando a oferta do guia de estudo quando o livro
tiver um StudyGuide publicado.
--}}

@props(['book' => null])

@php
    $guide = $book?->publishedGuide;
@endphp

<div id="download-popup-overlay" class="hidden fixed inset-0 z-[100] items-center justify-center"
    style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" onclick="closeDownloadPopup()">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full mx-4 overflow-hidden" style="max-width: 26rem;"
        onclick="event.stopPropagation()">

        {{-- Barra decorativa no topo --}}
        <div class="h-2" style="background: linear-gradient(to right, #004D40, #00695C, #B8860B);"></div>

        {{-- Botão fechar --}}
        <button onclick="closeDownloadPopup()"
            class="absolute flex items-center justify-center rounded-full transition-colors"
            style="top: 0.75rem; right: 0.75rem; width: 1.75rem; height: 1.75rem; background: #f9fafb; border: 1px solid #e5e7eb; cursor: pointer;"
            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
            <i class="ri-close-line" style="color: #9ca3af; font-size: 0.875rem; line-height: 1;"></i>
        </button>

        {{-- ======== ESTADO: Confirmação simples (sem guia publicado) ======== --}}
        <div id="popup-state-confirm" class="hidden" style="padding: 2rem;">
            <div class="text-center">
                <div class="flex justify-center" style="margin-bottom: 1.5rem;">
                    <div class="relative w-20 h-20">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center shadow-lg"
                            style="background: linear-gradient(135deg, #004D40, #00695C);">
                            <i class="ri-check-line text-white text-4xl"></i>
                        </div>
                        <div class="absolute inset-0 rounded-full border-2 animate-ping"
                            style="border-color: rgba(184, 134, 11, 0.3);"></div>
                    </div>
                </div>
                <h3 class="text-2xl font-bold" style="color: #333333; margin-bottom: 0.375rem;">Download Iniciado!</h3>
                <p style="color: #4b5563; margin-bottom: 0.25rem;">Obrigado por baixar <span id="confirm-book-title"
                        class="font-semibold" style="color: #004D40;">o livro</span>!</p>
                <p class="text-sm" style="color: #9ca3af;">Boa leitura!</p>
            </div>

            {{-- Divulgação temporária de curso parceiro (Hotmart) — só aparece
                 quando o livro não tem guia próprio publicado. Remover quando
                 os guias assumirem este espaço. --}}
            <div style="border-top: 1px solid #e5e7eb; margin-top: 1.5rem; padding-top: 1.25rem; text-align: center;">
                <x-partner-offer placement="download_popup" :extra="['book_id' => $book->id ?? null]" :auto-view="false" />
            </div>
        </div>

        @if ($guide)
            {{-- ======== ESTADO: Oferta do guia de estudo ======== --}}
            <div id="popup-state-offer" class="hidden" style="padding: 2rem;">
                <div class="text-center" style="margin-bottom: 1.25rem;">
                    <div class="flex justify-center" style="margin-bottom: 1rem;">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-lg"
                            style="background: linear-gradient(135deg, #004D40, #00695C);">
                            <i class="ri-check-line text-white text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold" style="color: #333333; margin-bottom: 0.375rem;">Download Iniciado!</h3>
                    <p style="color: #4b5563;">Obrigado por baixar <span id="offer-book-title" class="font-semibold"
                            style="color: #004D40;">o livro</span>!</p>
                </div>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.25rem;">
                    <div class="flex items-center justify-center gap-2" style="margin-bottom: 0.5rem;">
                        <i class="ri-file-text-line text-lg" style="color: #B8860B;"></i>
                        <span class="text-sm font-semibold" style="color: #333333;">{{ $guide->title }}</span>
                    </div>
                    <p class="text-sm leading-relaxed text-center" style="color: #6b7280; margin-bottom: 1.25rem;">
                        Quer aprofundar? Enviamos gratuito por e-mail um guia de estudo sobre este livro.
                    </p>

                    <form id="guide-capture-form" onsubmit="return submitGuideCapture(event)">
                        <input type="email" id="guide-capture-email" name="email" required placeholder="seu@email.com"
                            style="width: 100%; padding: 0.625rem 1rem; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; font-size: 0.9rem; margin-bottom: 0.75rem;">

                        <label class="flex items-start gap-2" style="margin-bottom: 1rem; cursor: pointer;">
                            <input type="checkbox" id="guide-capture-consent" required style="margin-top: 0.2rem;">
                            <span class="text-xs" style="color: #6b7280; text-align: left;">
                                Concordo com a <a href="{{ route('privacy.index') }}" target="_blank"
                                    style="color: #004D40; text-decoration: underline;">política de privacidade</a>
                                e quero receber o guia por e-mail.
                            </span>
                        </label>

                        <p id="guide-capture-error" class="hidden text-xs" style="color: #b91c1c; margin-bottom: 0.75rem;"></p>

                        <button type="submit" id="guide-capture-submit"
                            class="w-full text-white font-semibold rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center gap-2"
                            style="padding: 0.75rem 1.5rem; background: linear-gradient(to right, #B8860B, #A0750A); cursor: pointer;">
                            <i class="ri-mail-send-line text-xl"></i>Quero o guia grátis
                        </button>
                    </form>

                    <div style="margin-top: 0.875rem; text-align: center;">
                        <button onclick="closeDownloadPopup()" class="text-sm transition-colors"
                            style="color: #9ca3af; cursor: pointer;" onmouseover="this.style.color='#4b5563'"
                            onmouseout="this.style.color='#9ca3af'">Agora não, obrigado</button>
                    </div>
                </div>
            </div>

            {{-- ======== ESTADO: Guia entregue ======== --}}
            <div id="popup-state-delivered" class="hidden" style="padding: 2rem;">
                <div class="text-center">
                    <div class="flex justify-center" style="margin-bottom: 1.5rem;">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center shadow-lg"
                            style="background: linear-gradient(135deg, #004D40, #00695C);">
                            <i class="ri-mail-check-line text-white text-4xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold" style="color: #333333; margin-bottom: 0.375rem;">Confira seu e-mail!</h3>
                    <p style="color: #4b5563; margin-bottom: 1.5rem;">Enviamos o guia para você. Se preferir, baixe direto por
                        aqui:</p>
                    <a id="guide-delivered-download-link" href="#" target="_blank" rel="noopener"
                        class="w-full text-white font-semibold rounded-xl shadow-lg transition-all duration-200 inline-flex items-center justify-center gap-2"
                        style="padding: 0.75rem 1.5rem; background: linear-gradient(to right, #004D40, #00695C); cursor: pointer; text-decoration: none;">
                        <i class="ri-download-2-line text-xl"></i>Baixar guia agora
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    const _guidePopupHasGuide = @json((bool) $guide);
    const _guidePopupGuideId = @json($guide->id ?? null);
    const _guidePopupBookId = @json($book->id ?? null);
    const _guidePopupSlug = @json($guide->slug ?? null);
    let _guideDownloadUrl = null;

    // ── Navegação entre estados ─────────────────────────────────────
    function _showState(id) {
        ['popup-state-confirm', 'popup-state-offer', 'popup-state-delivered'].forEach(s => {
            const el = document.getElementById(s);
            if (el) el.classList.add('hidden');
        });
        const target = document.getElementById(id);
        if (target) target.classList.remove('hidden');
    }

    // ── API pública ─────────────────────────────────────────────────
    function handleDownload(btn) {
        const isBucket = btn.dataset.isBucket === '1';
        const url = btn.dataset.downloadUrl;
        const format = btn.dataset.format;
        const bookId = btn.dataset.bookId;
        const title = btn.dataset.bookTitle;

        if (typeof trackDownload === 'function') {
            trackDownload(bookId, title, format);
        }

        // O download nunca espera o popup: dispara na hora.
        if (isBucket) {
            const a = document.createElement('a');
            a.href = url;
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            // Nova aba: mantém a página atual viva para o popup poder abrir
            // (navegar na mesma aba tiraria o usuário do funil sem contagem).
            // Se o bloqueador de pop-up barrar, cai pra navegação direta —
            // o download não pode depender do pop-up ser permitido.
            const w = window.open(url, '_blank', 'noopener');
            if (!w) {
                window.location.href = url;
            }
        }

        showDownloadPopup(title);
    }

    function showDownloadPopup(title = 'o livro') {
        const overlay = document.getElementById('download-popup-overlay');

        ['confirm-book-title', 'offer-book-title'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = title;
        });

        if (_guidePopupHasGuide) {
            _showState('popup-state-offer');
            if (typeof trackGuideEvent === 'function') {
                trackGuideEvent('guide_offer_view', {
                    guide_id: _guidePopupGuideId,
                    book_id: _guidePopupBookId,
                    surface: 'popup',
                });
            }
        } else {
            _showState('popup-state-confirm');

            // Oferta do curso parceiro (ver x-partner-offer): só conta como
            // visualização quando o popup de fato abre neste estado, não a
            // cada carregamento de página.
            const offerEl = document.getElementById('partner-offer-download_popup');
            if (offerEl && typeof trackGuideEvent === 'function') {
                trackGuideEvent('partner_offer_view', JSON.parse(offerEl.dataset.tracking));
            }
        }

        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }

    function closeDownloadPopup() {
        const overlay = document.getElementById('download-popup-overlay');
        overlay.classList.remove('flex');
        overlay.classList.add('hidden');
    }

    // ── Captura de e-mail do guia ─────────────────────────────────────
    function submitGuideCapture(event) {
        event.preventDefault();
        if (!_guidePopupSlug) return false;

        const email = document.getElementById('guide-capture-email').value;
        const consent = document.getElementById('guide-capture-consent').checked;
        const errorEl = document.getElementById('guide-capture-error');
        const submitBtn = document.getElementById('guide-capture-submit');

        errorEl.classList.add('hidden');

        if (!consent) {
            errorEl.textContent = 'É preciso aceitar a política de privacidade para receber o guia.';
            errorEl.classList.remove('hidden');
            return false;
        }

        submitBtn.disabled = true;

        fetch('{{ $guide ? route('guias.capture', $guide->slug) : '#' }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: email, consent: consent, surface: 'popup' }),
        })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) throw new Error(data.message || 'Não foi possível concluir o cadastro.');

                if (typeof mixpanel !== 'undefined') {
                    mixpanel.track('guide_email_submit', { guide_id: _guidePopupGuideId, book_id: _guidePopupBookId, surface: 'popup' });
                }

                _guideDownloadUrl = data.download_url;
                const link = document.getElementById('guide-delivered-download-link');
                if (link) link.href = _guideDownloadUrl;

                _showState('popup-state-delivered');
            })
            .catch(err => {
                errorEl.textContent = err.message || 'Não foi possível concluir o cadastro. Tente novamente.';
                errorEl.classList.remove('hidden');
            })
            .finally(() => {
                submitBtn.disabled = false;
            });

        return false;
    }
</script>
