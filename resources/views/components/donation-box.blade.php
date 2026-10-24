{{--
Componente: Doação via PIX (valores, valor livre, copiar/QR, "Já doei")
Usado no popup pós-download e na página /apoie — a lógica é uma só (LLDonation).

Enxuto de propósito: uma fileira de valores (o campo "Outro valor" só aparece ao
clicar em "Outro"), um botão com o valor dentro, e a instrução do banco só depois
de copiar.

Mobile primeiro: em tela de toque o botão principal é "Copiar código PIX" e o
QR fica atrás de um link (a pessoa não consegue escanear a própria tela). No
desktop o QR vem primeiro e o botão de copiar é secundário.

Eventos (trackGuideEvent -> Mixpanel, GA e analytics_events), todos com
surface, book_id, value e, no popup, variant_id/variant_text/downloads_count:
donation_value, donation_qr_view, donation_copied, donation_claimed.
--}}

@props(['surface' => 'popup', 'bookId' => null])

@php
    $pixConfig = [
        'key'    => config('donation.pix.key'),
        'name'   => config('donation.pix.name'),
        'city'   => config('donation.pix.city'),
        'default' => (float) config('donation.default_amount'),
        'min'    => (float) config('donation.min_amount'),
        'max'    => (float) config('donation.max_amount'),
    ];
    $chipStyle = 'flex: 1; padding: 0.5rem 0; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 600; border: 2px solid #e5e7eb; background: #fff; color: #333333; transition: all 0.15s; cursor: pointer;';
@endphp

<div id="ll-donation" data-surface="{{ $surface }}" data-book-id="{{ $bookId }}">

    <div id="ll-don-form">
        {{-- Valores sugeridos, numa fileira só --}}
        <div style="display: flex; gap: 0.375rem; margin-bottom: 0.75rem;">
            @foreach (config('donation.amounts') as $amount)
                <button type="button" class="ll-don-chip" data-value="{{ $amount }}"
                    onclick="LLDonation.pick({{ $amount }})" style="{{ $chipStyle }}">R$ {{ $amount }}</button>
            @endforeach
            <button type="button" id="ll-don-other" onclick="LLDonation.other()" style="{{ $chipStyle }}">Outro</button>
        </div>

        {{-- Valor livre: escondido até clicar em "Outro" --}}
        <div id="ll-don-custom-wrap" style="display: none; margin-bottom: 0.75rem;">
            <input type="text" id="ll-don-custom" inputmode="decimal" autocomplete="off"
                placeholder="Valor em reais, ex.: 12,50" aria-label="Outro valor em reais"
                oninput="LLDonation.custom(this.value)"
                style="width: 100%; padding: 0.5rem 0.75rem; border: 1.5px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.9rem;">
            <p id="ll-don-error" class="text-xs" style="display: none; color: #b91c1c; margin-top: 0.25rem;"></p>
        </div>

        <div id="ll-don-pay" style="display: flex; flex-direction: column; gap: 0.625rem;">
            {{-- QR Code --}}
            <div id="ll-don-qr-wrap" class="flex justify-center" style="order: 1;">
                <div style="border-radius: 0.75rem; border: 1px solid #e5e7eb; display: inline-block; padding: 0.5rem; background: #fff;">
                    <img id="ll-don-qr-img" src="" alt="QR Code PIX" style="width: 150px; height: 150px; display: block;">
                </div>
            </div>

            {{-- Copiar código (o valor vai no próprio rótulo) --}}
            <button type="button" id="ll-don-copy" onclick="LLDonation.copy()"
                class="w-full flex items-center justify-center gap-2 transition-all duration-200"
                style="order: 2; cursor: pointer;">
                <i class="ri-file-copy-line"></i><span id="ll-don-copy-label">Copiar código PIX</span>
            </button>

            {{-- Só aparece em tela de toque --}}
            <button type="button" id="ll-don-qr-toggle" onclick="LLDonation.toggleQr()" class="text-sm"
                style="order: 3; display: none; color: #6b7280; cursor: pointer; text-decoration: underline;">
                Prefere ler o QR Code?
            </button>

            {{-- Aparece só depois de copiar --}}
            <p id="ll-don-hint" class="text-xs text-center" style="order: 4; display: none; color: #4b5563;">
                Agora é só abrir o app do seu banco: Pix &gt; Pix Copia e Cola &gt; colar o código.
            </p>

            <p class="text-xs text-center" style="order: 5; color: #9ca3af; word-break: break-all;">
                Chave PIX: <span style="color: #6b7280;">{{ config('donation.pix.key') }}</span>
            </p>
        </div>

        <div class="text-center" style="margin-top: 0.5rem;">
            <button type="button" onclick="LLDonation.claim()" class="text-sm"
                style="color: #004D40; cursor: pointer; text-decoration: underline;">Já fiz a doação</button>
        </div>
    </div>

    {{-- Agradecimento (autodeclarado: o PIX estático não permite confirmar o pagamento) --}}
    <div id="ll-don-thanks" class="text-center" style="display: none;">
        <i class="ri-heart-fill block" style="color: #B8860B; font-size: 2rem; margin-bottom: 0.5rem;"></i>
        <h4 class="text-lg font-bold" style="color: #333333; margin-bottom: 0.375rem;">Muito obrigado!</h4>
        <p class="text-sm" style="color: #4b5563;">Sua ajuda paga a estrutura do site e ajuda a trazer novas obras.</p>
    </div>
</div>

@once
<script>
    const LLDonation = (function () {
        const CFG = @json($pixConfig);
        const DONATED_KEY = 'll_donated_at';
        const DONATED_DAYS = 90;

        let value = CFG.default;
        let payload = '';
        let ctx = {};
        let qrShown = false;
        let qrTracked = false;
        let customTimer = null;
        let otherOpen = false;

        const $ = id => document.getElementById(id);
        const isTouch = () =>
            (window.matchMedia && window.matchMedia('(pointer: coarse)').matches) ||
            /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        const brl = v => 'R$ ' + v.toFixed(2).replace('.', ',');
        const short = v => Number.isInteger(v) ? 'R$ ' + v : brl(v);

        // ── Gerador de payload EMV BR Code (PIX válido - padrão BACEN) ──
        function pixField(id, v) {
            const len = String(v.length).padStart(2, '0');
            return `${id}${len}${v}`;
        }

        function crc16(str) {
            let crc = 0xFFFF;
            for (let i = 0; i < str.length; i++) {
                crc ^= str.charCodeAt(i) << 8;
                for (let j = 0; j < 8; j++) {
                    crc = (crc & 0x8000) ? ((crc << 1) ^ 0x1021) : (crc << 1);
                    crc &= 0xFFFF;
                }
            }
            return crc;
        }

        function gerarPayloadPix(chave, valor, nome, cidade) {
            const merchantInfo = pixField('00', 'br.gov.bcb.pix') + pixField('01', chave);
            const additionalData = pixField('05', '***');
            const valorStr = parseFloat(valor).toFixed(2);
            const nomeStr = nome.substring(0, 25).normalize('NFD').replace(/[̀-ͯ]/g, '');
            const cidadeStr = cidade.substring(0, 15).normalize('NFD').replace(/[̀-ͯ]/g, '');

            let p =
                pixField('00', '01') +
                pixField('26', merchantInfo) +
                pixField('52', '0000') +
                pixField('53', '986') +
                pixField('54', valorStr) +
                pixField('58', 'BR') +
                pixField('59', nomeStr) +
                pixField('60', cidadeStr) +
                pixField('62', additionalData) +
                '6304';

            return p + crc16(p).toString(16).toUpperCase().padStart(4, '0');
        }

        // ── Tracking ────────────────────────────────────────────────────
        function track(name, extra) {
            const root = $('ll-donation');
            const props = Object.assign({
                surface: root ? root.dataset.surface : null,
                book_id: root && root.dataset.bookId ? Number(root.dataset.bookId) : null,
            }, ctx, extra || {});

            if (typeof trackGuideEvent === 'function') {
                trackGuideEvent(name, props);
            } else if (typeof mixpanel !== 'undefined') {
                mixpanel.track(name, props);
            }
        }

        // ── Estado visual ───────────────────────────────────────────────
        function paint(btn, active) {
            btn.style.background = active ? '#004D40' : '#fff';
            btn.style.color = active ? '#fff' : '#333333';
            btn.style.borderColor = active ? '#004D40' : '#e5e7eb';
        }

        function setChips() {
            document.querySelectorAll('.ll-don-chip').forEach(btn => {
                paint(btn, !otherOpen && Number(btn.dataset.value) === value);
            });
            paint($('ll-don-other'), otherOpen);
        }

        function styleCopy(touch) {
            const btn = $('ll-don-copy');
            btn.style.padding = touch ? '0.875rem 1rem' : '0.625rem 1rem';
            btn.style.borderRadius = '0.75rem';
            btn.style.fontWeight = touch ? '700' : '500';
            btn.style.fontSize = touch ? '1rem' : '0.875rem';
            btn.style.border = touch ? 'none' : '1.5px solid #e5e7eb';
            btn.style.background = touch ? 'linear-gradient(to right, #B8860B, #A0750A)' : '#fff';
            btn.style.color = touch ? '#fff' : '#333333';
        }

        function render() {
            payload = gerarPayloadPix(CFG.key, value, CFG.name, CFG.city);
            $('ll-don-copy-label').textContent = 'Copiar código PIX · ' + short(value);
            setChips();
            if (qrShown) {
                $('ll-don-qr-img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=0&data=' + encodeURIComponent(payload);
            }
        }

        function showQr(show) {
            qrShown = show;
            $('ll-don-qr-wrap').style.display = show ? 'flex' : 'none';
            if (show) {
                render();
                if (!qrTracked) {
                    qrTracked = true;
                    track('donation_qr_view', { value: value });
                }
            }
        }

        function setError(msg) {
            const el = $('ll-don-error');
            el.textContent = msg || '';
            el.style.display = msg ? 'block' : 'none';
            $('ll-don-custom').style.borderColor = msg ? '#b91c1c' : '#e5e7eb';
            $('ll-don-copy').style.opacity = msg ? '0.5' : '1';
            $('ll-don-copy').style.pointerEvents = msg ? 'none' : 'auto';
        }

        function setOther(open) {
            otherOpen = open;
            $('ll-don-custom-wrap').style.display = open ? 'block' : 'none';
        }

        // ── API ─────────────────────────────────────────────────────────
        function pick(v) {
            $('ll-don-custom').value = '';
            setOther(false);
            setError('');
            value = v;
            render();
            track('donation_value', { value: value, source: 'chip' });
        }

        function other() {
            setOther(true);
            setChips();
            $('ll-don-custom').focus();
        }

        function custom(raw) {
            clearTimeout(customTimer);
            const text = String(raw).trim().replace(',', '.');
            if (text === '') { setError(''); value = CFG.default; render(); return; }

            const v = Math.round(parseFloat(text) * 100) / 100;
            if (!isFinite(v) || v < CFG.min || v > CFG.max) {
                setError('Informe um valor entre ' + short(CFG.min) + ' e ' + short(CFG.max) + '.');
                return;
            }

            setError('');
            value = v;
            render();
            // Debounce: evita um evento por tecla digitada.
            customTimer = setTimeout(() => track('donation_value', { value: value, source: 'custom' }), 700);
        }

        function copy() {
            const label = $('ll-don-copy-label');
            const done = () => {
                $('ll-don-hint').style.display = 'block';
                label.textContent = 'Copiado!';
                setTimeout(() => { render(); }, 2500);
                track('donation_copied', { value: value });
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(payload).then(done).catch(fallback);
            } else {
                fallback();
            }

            function fallback() {
                const ta = document.createElement('textarea');
                ta.value = payload;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                done();
            }
        }

        function toggleQr() {
            showQr(!qrShown);
            $('ll-don-qr-toggle').textContent = qrShown ? 'Esconder QR Code' : 'Prefere ler o QR Code?';
        }

        function claim() {
            try { localStorage.setItem(DONATED_KEY, String(Date.now())); } catch (e) {}
            track('donation_claimed', { value: value });
            $('ll-don-form').style.display = 'none';
            $('ll-don-thanks').style.display = 'block';
        }

        function hasDonated() {
            try {
                const at = Number(localStorage.getItem(DONATED_KEY));
                return at > 0 && (Date.now() - at) < DONATED_DAYS * 86400000;
            } catch (e) { return false; }
        }

        function open(context) {
            ctx = context || {};
            value = CFG.default;
            qrTracked = false;
            $('ll-don-custom').value = '';
            setOther(false);
            $('ll-don-hint').style.display = 'none';
            $('ll-don-form').style.display = 'block';
            $('ll-don-thanks').style.display = 'none';
            setError('');

            const touch = isTouch();
            styleCopy(touch);
            $('ll-don-copy').style.order = touch ? '1' : '2';
            $('ll-don-qr-wrap').style.order = touch ? '2' : '1';
            $('ll-don-qr-toggle').style.display = touch ? 'inline' : 'none';
            $('ll-don-qr-toggle').textContent = 'Prefere ler o QR Code?';

            showQr(!touch);
            render();
        }

        return { open, pick, other, custom, copy, toggleQr, claim, hasDonated, track,
                 setContext: c => { ctx = c || {}; } };
    })();
</script>
@endonce
