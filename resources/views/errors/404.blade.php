@extends('layouts.app')

@section('title', 'Página Não Encontrada')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center bg-gradient-to-br from-[#F8F9FA] to-[#E8F5E8] py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-8 relative inline-block">
            <h1 class="text-9xl font-bold text-[#004D40] opacity-10 select-none">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="ri-book-open-line text-6xl text-[#B8860B] animate-bounce"></i>
            </div>
        </div>
        
        <h2 class="text-4xl font-bold text-[#333333] mb-6">Página Não Encontrada</h2>
        <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
            Ops! Parece que o livro ou página que você está procurando não está em nossa estante. Pode ter sido movido, excluído ou talvez nunca tenha existido.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-8 py-3 text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <i class="ri-home-4-line mr-2"></i>Voltar ao Início
            </a>
            <a href="{{ route('livros.index') }}" class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-8 py-3 text-lg rounded-xl">
                <i class="ri-search-line mr-2"></i>Explorar Livros
            </a>
        </div>
    </div>
</section>
@endsection
