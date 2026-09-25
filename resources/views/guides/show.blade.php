@extends('layouts.app')

@section('title', $guide->title)

@section('seo')
    <x-seo-meta
        title="{{ $guide->title }} — Guia de Estudo Grátis | {{ $guide->book->title }}"
        description="{{ Str::limit('Baixe grátis o guia de estudo de ' . $guide->book->title . ' por e-mail: resumo, análise e material de apoio para leitura e vestibular.', 155) }}"
        :image="$guide->book->cover"
        type="article"
    />
@endsection

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-1 rounded-full"
                style="background: rgba(0,77,64,0.08); color: #004D40;">
                <i class="ri-file-text-line"></i> Guia de estudo gratuito
            </span>
            <h1 class="text-3xl sm:text-4xl font-bold mt-4 mb-3" style="color: #333333;">{{ $guide->title }}</h1>
            <p class="text-lg text-gray-600">
                Baseado em <a href="{{ route('livros.show', $guide->book->slug) }}" class="font-medium"
                    style="color: #004D40;">{{ $guide->book->title }}</a>
            </p>
        </div>

        <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-8 border border-gray-200 mb-10">
            <div id="guide-landing-form-wrapper">
                <h2 class="text-xl font-semibold mb-2" style="color: #333333;">Receba o guia por e-mail</h2>
                <p class="text-sm text-gray-600 mb-6">Resumo, análise e material de apoio para ler {{ $guide->book->title }}
                    com mais profundidade. Grátis, na hora.</p>

                <form id="guide-landing-form" onsubmit="return submitLandingCapture(event)" class="max-w-md">
                    <input type="email" id="landing-capture-email" name="email" required placeholder="seu@email.com"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent mb-3">

                    <label class="flex items-start gap-2 mb-4 cursor-pointer">
                        <input type="checkbox" id="landing-capture-consent" required class="mt-1">
                        <span class="text-xs text-gray-500 text-left">
                            Concordo com a <a href="{{ route('privacy.index') }}" target="_blank"
                                class="underline" style="color: #004D40;">política de privacidade</a>
                            e quero receber o guia por e-mail.
                        </span>
                    </label>

                    <p id="landing-capture-error" class="hidden text-sm text-red-700 mb-3"></p>

                    <button type="submit" id="landing-capture-submit"
                        class="inline-flex items-center justify-center gap-2 font-semibold text-white rounded-xl shadow-lg px-6 py-3 w-full"
                        style="background: linear-gradient(to right, #B8860B, #A0750A);">
                        <i class="ri-mail-send-line text-xl"></i>Quero o guia grátis
                    </button>
                </form>
            </div>

            <div id="guide-landing-success" class="hidden text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-lg"
                        style="background: linear-gradient(135deg, #004D40, #00695C);">
                        <i class="ri-mail-check-line text-white text-3xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-2" style="color: #333333;">Confira seu e-mail!</h3>
                <p class="text-gray-600 mb-6">Enviamos o guia para você. Se preferir, baixe direto por aqui:</p>
                <a id="guide-landing-download-link" href="#" target="_blank" rel="noopener"
                    class="inline-flex items-center justify-center gap-2 font-semibold text-white rounded-xl shadow-lg px-6 py-3"
                    style="background: linear-gradient(to right, #004D40, #00695C); text-decoration: none;">
                    <i class="ri-download-2-line text-xl"></i>Baixar guia agora
                </a>
            </div>
        </div>

        <p class="text-center">
            <a href="{{ route('livros.show', $guide->book->slug) }}" class="text-sm text-gray-500 hover:underline">
                <i class="ri-arrow-left-line"></i> Voltar para {{ $guide->book->title }}
            </a>
        </p>
    </div>

    <script>
        function submitLandingCapture(event) {
            event.preventDefault();

            const email = document.getElementById('landing-capture-email').value;
            const consent = document.getElementById('landing-capture-consent').checked;
            const errorEl = document.getElementById('landing-capture-error');
            const submitBtn = document.getElementById('landing-capture-submit');

            errorEl.classList.add('hidden');

            if (!consent) {
                errorEl.textContent = 'É preciso aceitar a política de privacidade para receber o guia.';
                errorEl.classList.remove('hidden');
                return false;
            }

            submitBtn.disabled = true;

            fetch('{{ route('guias.capture', $guide->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ email: email, consent: consent, surface: 'landing' }),
            })
                .then(response => response.json().then(data => ({ ok: response.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok) throw new Error(data.message || 'Não foi possível concluir o cadastro.');

                    if (typeof mixpanel !== 'undefined') {
                        mixpanel.track('guide_email_submit', {
                            guide_id: {{ $guide->id }},
                            book_id: {{ $guide->book_id }},
                            surface: 'landing',
                        });
                    }

                    document.getElementById('guide-landing-download-link').href = data.download_url;
                    document.getElementById('guide-landing-form-wrapper').classList.add('hidden');
                    document.getElementById('guide-landing-success').classList.remove('hidden');
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
@endsection
