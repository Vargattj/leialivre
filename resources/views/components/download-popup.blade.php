{{--
    Componente: Download Popup
    Uso: <x-download-popup />

    Funções JS disponíveis globalmente após incluir este componente:
      - handleDownload(btn)       → chamar no onclick do botão de download
      - showDownloadPopup(url)    → abrir o popup e iniciar o download
      - closeDownloadPopup()      → fechar o popup manualmente
--}}

<div id="download-popup-overlay"
     class="hidden fixed inset-0 z-[100] items-center justify-center"
     style="background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
     onclick="closeDownloadPopup()">

    <div class="bg-white w-full mx-5 text-center"
         style="max-width: 420px; border-radius: 24px; box-shadow: 0 32px 64px -12px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.04);"
         onclick="event.stopPropagation()">

        {{-- ── Stage 1: Baixando ── --}}
        <div id="popup-stage-downloading" style="padding: 40px 36px 36px;">

            {{-- Ícone animado --}}
            <div style="display:flex; justify-content:center; margin-bottom: 28px;">
                <div style="position: relative; width: 72px; height: 72px;">
                    {{-- fundo suave --}}
                    <div style="position:absolute; inset:0; border-radius:50%; background: rgba(0,77,64,0.07);"></div>
                    {{-- anel estático --}}
                    <div style="position:absolute; inset:0; border-radius:50%; border: 2.5px solid #e5e7eb;"></div>
                    {{-- anel girando --}}
                    <div class="dl-spinner" style="position:absolute; inset:0; border-radius:50%; border: 2.5px solid transparent; border-top-color: #004D40;"></div>
                    {{-- ícone central --}}
                    <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                        <i class="ri-download-cloud-2-line" style="font-size: 26px; color: #004D40;"></i>
                    </div>
                </div>
            </div>

            <p style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 6px;">Preparando o download</p>
            <p style="font-size: 13px; color: #9ca3af; margin: 0 0 28px; line-height: 1.5;">Seu livro está sendo carregado, aguarde um instante&hellip;</p>

            {{-- Barra de progresso --}}
            <div style="width: 100%; background: #f3f4f6; border-radius: 999px; height: 3px; overflow: hidden;">
                <div id="download-progress-bar"
                     style="height: 100%; background: #B8860B; border-radius: 999px; width: 0%;
                            animation: dlProgress 2s cubic-bezier(.4,0,.2,1) forwards;"></div>
            </div>
        </div>

        {{-- ── Stage 2: Concluído ── --}}
        <div id="popup-stage-done" class="dl-fade-in" style="display: none; padding: 40px 36px 36px;">

            {{-- Ícone de sucesso --}}
            <div style="display:flex; justify-content:center; margin-bottom: 24px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(34,197,94,0.1);
                            display:flex; align-items:center; justify-content:center;">
                    <i class="ri-check-line" style="font-size: 36px; color: #22c55e; font-weight: 700;"></i>
                </div>
            </div>

            <p style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 8px;">Download iniciado!</p>
            <p style="font-size: 13px; color: #9ca3af; margin: 0 0 28px; line-height: 1.6;">
                Obrigado por usar o <span style="font-weight: 600; color: #004D40;">Leia Livre</span>.
            </p>

            <button onclick="closeDownloadPopup()"
                    style="width: 100%; padding: 14px; background: #004D40; color: #fff; font-size: 15px;
                           font-weight: 600; border: none; border-radius: 14px; cursor: pointer;
                           transition: background .15s, transform .1s;"
                    onmouseover="this.style.background='#003830'"
                    onmouseout="this.style.background='#004D40'"
                    onmousedown="this.style.transform='scale(0.98)'"
                    onmouseup="this.style.transform='scale(1)'">
                Fechar
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes dlProgress {
        from { width: 0%; }
        to   { width: 100%; }
    }
    @keyframes dlFadeUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .dl-spinner {
        animation: dlSpin .75s linear infinite;
    }
    @keyframes dlSpin {
        to { transform: rotate(360deg); }
    }
    .dl-fade-in {
        animation: dlFadeUp .3s ease both;
    }
</style>

<script>
    function handleDownload(btn) {
        const isBucket = btn.dataset.isBucket === '1';
        const url      = btn.dataset.downloadUrl;
        const format   = btn.dataset.format;
        const bookId   = btn.dataset.bookId;
        const title    = btn.dataset.bookTitle;

        if (typeof trackDownload === 'function') {
            trackDownload(bookId, title, format);
        }

        if (isBucket) {
            showDownloadPopup(url);
        } else {
            window.location.href = url;
        }
    }

    function showDownloadPopup(url) {
        const overlay          = document.getElementById('download-popup-overlay');
        const stageDownloading = document.getElementById('popup-stage-downloading');
        const stageDone        = document.getElementById('popup-stage-done');

        // Reset
        stageDownloading.style.display = 'block';
        stageDone.style.display        = 'none';
        overlay.style.display          = 'flex';

        // Reinicia animação da barra via clone
        const bar = document.getElementById('download-progress-bar');
        if (bar) {
            const clone = bar.cloneNode(true);
            bar.parentNode.replaceChild(clone, bar);
        }

        // Dispara download via <a download> — não navega para fora da página
        const a    = document.createElement('a');
        a.href     = url;
        a.download = '';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        // Após 2s exibe stage de conclusão
        setTimeout(() => {
            stageDownloading.style.display = 'none';
            stageDone.style.display        = 'block';
        }, 2000);
    }

    function closeDownloadPopup() {
        const overlay = document.getElementById('download-popup-overlay');
        overlay.style.display = 'none';
    }
</script>
