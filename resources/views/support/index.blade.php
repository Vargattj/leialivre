@extends('layouts.app')

@section('title', 'Apoie o Leia Livre')

@section('seo')
    <x-seo-meta
        title="Apoie o Leia Livre | Leia Livre"
        description="Todos os livros do Leia Livre são gratuitos. Veja quanto custa manter o site no ar e como ajudar com uma doação via PIX."
        keywords="apoiar, doação, pix, leia livre, livros gratuitos"
    />
@endsection

@php
    $costs         = \App\Support\Donation::costs();
    $monthlyCost   = \App\Support\Donation::monthlyCost();
    $exampleAmount = (float) config('donation.example_amount');
    $daysCovered   = \App\Support\Donation::daysCovered($exampleAmount);
@endphp

@section('content')
    <div class="bg-gradient-to-br from-[#004D40] to-[#00695C] text-white py-14">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <i class="ri-heart-fill text-3xl" style="color: #E9C46A;"></i>
            <h1 class="text-3xl md:text-4xl font-bold mt-3 mb-4">Apoie o Leia Livre</h1>
            <p class="text-lg text-white/90 leading-relaxed">
                Todos os livros aqui são gratuitos e vão continuar sendo. As doações pagam a estrutura que mantém o site no ar.
            </p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">

        <section>
            <h2 class="text-2xl font-bold text-[#004D40] mb-3">Como o site se mantém</h2>
            <p class="text-gray-700 leading-relaxed mb-3">
                O Leia Livre reúne obras em domínio público, com contexto sobre cada livro e autor, em formatos prontos
                para baixar. Ele é mantido por uma pessoa, e não há acesso pago para ler ou baixar os livros.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Uma doação, de qualquer valor, ajuda a pagar o servidor e a incluir novas obras. Ela é voluntária e não
                muda em nada o que você pode acessar no site.
            </p>
        </section>

        @if ($costs)
            <section>
                <h2 class="text-2xl font-bold text-[#004D40] mb-3">Quanto custa manter</h2>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach ($costs as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-3 text-gray-700">{{ $item['label'] }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">{{ \App\Support\Donation::money($item['monthly']) }}/mês</td>
                                </tr>
                            @endforeach
                            <tr class="bg-[#FDFBF6]">
                                <td class="px-4 py-3 font-semibold text-[#333333]">Total</td>
                                <td class="px-4 py-3 text-right font-semibold text-[#333333]">{{ \App\Support\Donation::money($monthlyCost) }}/mês</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-gray-600 mt-3">
                    Na prática, {{ \App\Support\Donation::money($exampleAmount) }} mantêm o site no ar por cerca de {{ $daysCovered }} dias.
                    Não contamos o tempo de trabalho: ele é voluntário.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#004D40] mb-3">Meta do mês</h2>
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <x-donation-progress />
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    O valor é atualizado manualmente, com o que foi recebido no mês.
                </p>
            </section>
        @endif

        <section>
            <h2 class="text-2xl font-bold text-[#004D40] mb-4">Doe via PIX</h2>
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mx-auto" style="max-width: 26rem;">
                <x-donation-box surface="page" />
            </div>
            <p class="text-xs text-gray-500 text-center mt-4">
                O PIX vai direto para a chave exibida. O site não recebe nem guarda dados do pagamento.
            </p>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            LLDonation.track('donation_page_view');
            LLDonation.open({});
        });
    </script>
@endsection
