{{--
Componente: Download Popup
Uso: <x-download-popup />

Funções JS disponíveis globalmente após incluir este componente:
- handleDownload(btn) → chamar no onclick do botão de download
- showDownloadPopup(url, title) → abrir o popup e iniciar o download
- closeDownloadPopup() → fechar o popup manualmente
--}}

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

        {{-- ======== ESTADO 1: Confirmação de download ======== --}}
        <div id="popup-state-download" style="padding: 2rem;">
            <div class="text-center">

                {{-- Ícone circular com anel de ping --}}
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
                <p style="color: #4b5563; margin-bottom: 0.25rem;">Obrigado por baixar <span id="download-book-title"
                        class="font-semibold" style="color: #004D40;">o livro</span>!</p>
                <p class="text-sm" style="color: #9ca3af; margin-bottom: 2rem;">Boa leitura!</p>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                    <div class="flex items-center justify-center gap-2" style="margin-bottom: 0.75rem;">
                        <i class="ri-heart-fill text-lg" style="color: #B8860B;"></i>
                        <span class="text-sm font-medium" style="color: #333333;">Apoie o projeto</span>
                        <i class="ri-heart-fill text-lg" style="color: #B8860B;"></i>
                    </div>

                    <p class="text-sm leading-relaxed" style="color: #6b7280; margin-bottom: 1.25rem;">
                        Pesquisamos e curamos obras em domínio público para que você possa encontrá-las facilmente. Sua
                        doação mantém o site no ar.
                    </p>

                    <button onclick="showPopupPix()"
                        class="w-full text-white font-semibold rounded-xl shadow-lg transition-all duration-200 flex items-center justify-center gap-2"
                        style="padding: 0.75rem 1.5rem; background: linear-gradient(to right, #B8860B, #A0750A); cursor: pointer;"
                        onmouseover="this.style.background='linear-gradient(to right, #A0750A, #8F6408)';"
                        onmouseout="this.style.background='linear-gradient(to right, #B8860B, #A0750A)';">
                        <i class="ri-heart-line text-xl"></i>Fazer uma doação
                    </button>

                    <div style="margin-top: 1rem;">
                        <button onclick="closeDownloadPopup()" class="text-sm transition-colors"
                            style="color: #9ca3af; cursor: pointer;" onmouseover="this.style.color='#4b5563'"
                            onmouseout="this.style.color='#9ca3af'">Continuar navegando</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======== ESTADO 2: Seleção de valor PIX ======== --}}
        <div id="popup-state-pix" class="hidden" style="padding: 2rem;">

            <div class="text-center" style="margin-bottom: 1.5rem;">
                <i class="ri-heart-fill block" style="color: #B8860B; font-size: 1.5rem; margin-bottom: 0.625rem;"></i>
                <h3 class="text-xl font-bold" style="color: #333333; margin-bottom: 0.375rem;">Doação via PIX</h3>
                <p class="text-sm" style="color: #6b7280;">Escolha um valor e ajude a manter o site no ar</p>
            </div>

            {{-- Grid de valores: 3 na 1ª linha, 2 na 2ª --}}
            <div style="margin-bottom: 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 0.5rem;">
                    @foreach ([5, 15, 30] as $valor)
                        <button onclick="selectPixValue(this, {{ $valor }})" class="pix-value-btn"
                            style="padding: 0.625rem 0; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; border: 2px solid #e5e7eb; background: #fff; color: #333333; transition: all 0.15s; cursor: pointer;"
                            data-value="{{ $valor }}">
                            R$ {{ $valor }}
                        </button>
                    @endforeach
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                    @foreach ([50, 100] as $valor)
                        <button onclick="selectPixValue(this, {{ $valor }})" class="pix-value-btn"
                            style="padding: 0.625rem 0; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; border: 2px solid #e5e7eb; background: #fff; color: #333333; transition: all 0.15s; cursor: pointer;"
                            data-value="{{ $valor }}">
                            R$ {{ $valor }}
                        </button>
                    @endforeach
                </div>
            </div>

            <button onclick="gerarQrCodePix()"
                class="w-full text-white font-bold flex items-center justify-center gap-2 rounded-xl shadow-lg transition-all duration-200"
                style="padding: 0.75rem 1.5rem; background: linear-gradient(to right, #B8860B, #A0750A); cursor: pointer;"
                onmouseover="this.style.background='linear-gradient(to right, #A0750A, #8F6408)';"
                onmouseout="this.style.background='linear-gradient(to right, #B8860B, #A0750A)';">
                <i class="ri-qr-code-line text-lg"></i>Gerar QR Code PIX
            </button>

            <div class="text-center" style="margin-top: 1rem;">
                <button onclick="showPopupDownload()" class="text-sm transition-colors"
                    style="color: #9ca3af; cursor: pointer;" onmouseover="this.style.color='#4b5563'"
                    onmouseout="this.style.color='#9ca3af'">
                    <i class="ri-arrow-left-line" style="margin-right: 0.25rem;"></i>Voltar
                </button>
            </div>
        </div>

        {{-- ======== ESTADO 3: QR Code PIX ======== --}}
        <div id="popup-state-qrcode" class="hidden" style="padding: 2rem;">

            <div class="text-center" style="margin-bottom: 1.25rem;">
                <i class="ri-heart-fill block" style="color: #B8860B; font-size: 1.5rem; margin-bottom: 0.625rem;"></i>
                <h3 class="text-xl font-bold" style="color: #333333; margin-bottom: 0.375rem;">Doação via PIX</h3>
                <p class="text-sm" style="color: #6b7280;">Escolha um valor e ajude a manter o site no ar</p>
            </div>

            {{-- QR Code --}}
            <div class="flex justify-center" style="margin-bottom: 1.25rem;">
                <div
                    style="border-radius: 0.75rem; border: 1px solid #e5e7eb; display: inline-block; padding: 0.625rem; background: #fff;">
                    <img id="popup-qrcode-img" src="" alt="QR Code PIX"
                        style="width: 168px; height: 168px; display: block;">
                </div>
            </div>

            {{-- Chave PIX --}}
            <div
                style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem 1rem; text-align: center; margin-bottom: 0.625rem;">
                <p class="text-xs font-semibold"
                    style="color: #9ca3af; letter-spacing: 0.08em; margin-bottom: 0.25rem;">CHAVE PIX</p>
                <p class="font-medium" style="color: #333333;">leialivresite@gmail.com</p>
            </div>

            {{-- Botão copiar --}}
            <button id="popup-copy-btn" onclick="copiarChavePix()"
                class="w-full flex items-center justify-center gap-2 transition-all duration-200"
                style="padding: 0.625rem 1rem; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; background: #fff; color: #333333; font-size: 0.875rem; font-weight: 500; cursor: pointer;"
                onmouseover="this.style.borderColor='#004D40'; this.style.color='#004D40';"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#333333';">
                <i class="ri-file-copy-line"></i>Copiar código PIX
            </button>

            <div class="text-center" style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.375rem;">
                <button onclick="showPopupPix()" class="text-sm transition-colors"
                    style="color: #9ca3af; cursor: pointer;" onmouseover="this.style.color='#4b5563'"
                    onmouseout="this.style.color='#9ca3af'">
                    Escolher outro valor
                </button>
                <button onclick="showPopupDownload()" class="text-sm transition-colors"
                    style="color: #9ca3af; cursor: pointer;" onmouseover="this.style.color='#4b5563'"
                    onmouseout="this.style.color='#9ca3af'">
                    <i class="ri-arrow-left-line" style="margin-right: 0.25rem;"></i>Voltar
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    // ── Configuração ────────────────────────────────────────────────
    const _PIX_CHAVE = 'leialivresite@gmail.com';
    const _PIX_NOME = 'Leia livre';
    const _PIX_CIDADE = 'Porto Alegre';
    let _pixValor = 15;
    let _pixPayloadAtual = '';

    // ── Gerador de payload EMV BR Code (PIX válido - padrão BACEN) ──
    function _pixField(id, value) {
        const len = String(value.length).padStart(2, '0');
        return `${id}${len}${value}`;
    }

    function _crc16(str) {
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

    function _gerarPayloadPix(chave, valor, nome, cidade) {
        // Campo 26: Merchant Account Information (PIX)
        const merchantInfo =
            _pixField('00', 'br.gov.bcb.pix') +
            _pixField('01', chave);

        // Campo 62: Additional Data (txid obrigatório)
        const additionalData = _pixField('05', '***');

        // Monta o payload sem o CRC
        const valorStr = parseFloat(valor).toFixed(2);
        const nomeStr = nome.substring(0, 25).normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const cidadeStr = cidade.substring(0, 15).normalize('NFD').replace(/[\u0300-\u036f]/g, '');

        let payload =
            _pixField('00', '01') +                    // Payload Format Indicator
            _pixField('26', merchantInfo) +             // Merchant Account Info
            _pixField('52', '0000') +                  // MCC (genérico)
            _pixField('53', '986') +                   // Currency BRL
            _pixField('54', valorStr) +                // Valor
            _pixField('58', 'BR') +                    // Country
            _pixField('59', nomeStr) +                 // Merchant Name
            _pixField('60', cidadeStr) +               // Merchant City
            _pixField('62', additionalData) +          // Additional Data
            '6304';                                    // CRC placeholder

        const crc = _crc16(payload).toString(16).toUpperCase().padStart(4, '0');
        return payload + crc;
    }

    // ── Navegação entre estados ─────────────────────────────────────
    function _showState(id) {
        ['popup-state-download', 'popup-state-pix', 'popup-state-qrcode'].forEach(s => {
            document.getElementById(s).classList.add('hidden');
        });
        document.getElementById(id).classList.remove('hidden');
    }

    function showPopupDownload() { _showState('popup-state-download'); }

    function showPopupPix() {
        _showState('popup-state-pix');
        document.querySelectorAll('.pix-value-btn').forEach(btn => {
            _resetPixBtn(btn);
            if (parseInt(btn.dataset.value) === _pixValor) _activatePixBtn(btn);
        });
    }

    // ── Seleção de valor ────────────────────────────────────────────
    function selectPixValue(btn, valor) {
        _pixValor = valor;
        document.querySelectorAll('.pix-value-btn').forEach(b => _resetPixBtn(b));
        _activatePixBtn(btn);
    }

    function _activatePixBtn(btn) {
        btn.style.background = '#004D40';
        btn.style.color = '#fff';
        btn.style.borderColor = '#004D40';
    }

    function _resetPixBtn(btn) {
        btn.style.background = '#fff';
        btn.style.color = '#333333';
        btn.style.borderColor = '#e5e7eb';
    }

    // ── Gerar QR Code com payload PIX válido ────────────────────────
    function gerarQrCodePix() {
        _pixPayloadAtual = _gerarPayloadPix(_PIX_CHAVE, _pixValor, _PIX_NOME, _PIX_CIDADE);
        const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=0&data=' + encodeURIComponent(_pixPayloadAtual);

        document.getElementById('popup-qrcode-img').src = qrUrl;

        const copyBtn = document.getElementById('popup-copy-btn');
        copyBtn.innerHTML = '<i class="ri-file-copy-line"></i>Copiar código PIX';
        copyBtn.style.color = '#333333';
        copyBtn.style.borderColor = '#e5e7eb';

        _showState('popup-state-qrcode');
    }

    // ── Copiar payload PIX (código "copia e cola") ──────────────────
    function copiarChavePix() {
        const texto = _pixPayloadAtual || _PIX_CHAVE;
        const btn = document.getElementById('popup-copy-btn');

        navigator.clipboard.writeText(texto)
            .then(() => {
                btn.innerHTML = '<i class="ri-check-line"></i>Copiado!';
                btn.style.color = '#004D40';
                btn.style.borderColor = '#004D40';
                setTimeout(() => {
                    btn.innerHTML = '<i class="ri-file-copy-line"></i>Copiar código PIX';
                    btn.style.color = '#333333';
                    btn.style.borderColor = '#e5e7eb';
                }, 2500);
            })
            .catch(() => {
                // Fallback para navegadores sem clipboard API
                const ta = document.createElement('textarea');
                ta.value = texto;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                btn.innerHTML = '<i class="ri-check-line"></i>Copiado!';
                btn.style.color = '#004D40';
                btn.style.borderColor = '#004D40';
                setTimeout(() => {
                    btn.innerHTML = '<i class="ri-file-copy-line"></i>Copiar código PIX';
                    btn.style.color = '#333333';
                    btn.style.borderColor = '#e5e7eb';
                }, 2500);
            });
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

        if (isBucket) {
            showDownloadPopup(url, title);
        } else {
            window.location.href = url;
        }
    }

    function showDownloadPopup(url, title = 'o livro') {
        const overlay = document.getElementById('download-popup-overlay');
        const titleEl = document.getElementById('download-book-title');

        if (titleEl) titleEl.textContent = title;

        showPopupDownload();

        overlay.classList.remove('hidden');
        overlay.classList.add('flex');

        // Dispara download via <a download> — não navega para fora da página
        const a = document.createElement('a');
        a.href = url;
        a.download = '';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function closeDownloadPopup() {
        const overlay = document.getElementById('download-popup-overlay');
        overlay.classList.remove('flex');
        overlay.classList.add('hidden');
    }
</script>