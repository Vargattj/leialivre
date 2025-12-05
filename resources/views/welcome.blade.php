@extends('layouts.app')

@section('seo')
    <x-seo-meta
        title="Leia Livre - Biblioteca Digital Gratuita de Livros em Domínio Público"
        description="Descubra e baixe gratuitamente mais de 15.000 livros clássicos em domínio público. Obras de Shakespeare, Machado de Assis e mais. Múltiplos formatos: PDF, EPUB, MOBI."
        keywords="livros grátis, domínio público, literatura clássica, download livros, ebooks gratuitos, PDF, EPUB, MOBI, Shakespeare, Machado de Assis, livros clássicos"
        :image="asset('images/og-default.jpg')"
        type="website"
        :jsonLd="[
            [
                'type' => 'WebSite',
                'data' => [
                    'name' => 'Leia Livre',
                    'description' => 'Biblioteca digital gratuita de livros em domínio público com mais de 15.000 obras clássicas disponíveis para download',
                    'url' => url('/'),
                    'search_url' => route('livros.buscar') . '?q={search_term_string}',
                ]
            ],
            [
                'type' => 'Organization',
                'data' => [
                    'name' => 'Leia Livre',
                    'url' => url('/'),
                    'logo' => asset('images/logo.png'),
                    'sameAs' => [
                        // Adicione suas redes sociais aqui quando disponíveis
                        // 'https://facebook.com/leialivre',
                        // 'https://twitter.com/leialivre',
                    ]
                ]
            ]
        ]"
    />
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-[#F8F9FA] to-[#E8F5E8] py-20 lg:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-[#004D40] mb-6 leading-tight">
                Descubra os Maiores
                <span class="block text-[#B8860B] mt-2">Tesouros Literários</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 mb-12 leading-relaxed max-w-3xl mx-auto">
                Explore milhares de livros em domínio público com rico contexto histórico, biografias detalhadas de autores e múltiplos formatos de download. Sua porta de entrada para a literatura atemporal.
            </p>
            
            <div class="mb-12">
                <form action="{{ route('livros.buscar') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ri-search-line text-gray-400 text-xl"></i>
                        </div>
                        <input 
                            name="q"
                            placeholder="Buscar livros, autores ou assuntos..." 
                            class="block w-full pl-12 pr-32 py-4 text-lg border-2 border-gray-200 rounded-2xl placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent bg-white shadow-lg" 
                            type="text" 
                            value="{{ request('q') }}"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-6 py-3 text-lg rounded-xl" type="submit">
                                Buscar
                            </button>
                        </div>
                    </div>
                </form>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <span class="text-sm text-gray-500">Buscas populares:</span>
                    <a href="{{ route('livros.buscar', ['q' => 'Shakespeare']) }}" class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Shakespeare</a>
                    <a href="{{ route('livros.buscar', ['q' => 'Machado de Assis']) }}" class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Machado de Assis</a>
                    <a href="{{ route('livros.buscar', ['q' => 'Literatura Clássica']) }}" class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Literatura Clássica</a>
                    <a href="{{ route('livros.buscar', ['q' => 'Poesia']) }}" class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Poesia</a>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                <a href="/categories" class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#B8860B] text-white hover:bg-[#DAA520] focus:ring-2 focus:ring-[#B8860B]/20 px-6 py-3 text-lg rounded-lg min-w-[200px]">
                    <i class="ri-grid-line mr-2"></i>Explorar Categorias
                </a>
                <a href="{{ route('autores.index') }}" class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-6 py-3 text-lg rounded-lg min-w-[200px]">
                    <i class="ri-user-line mr-2"></i>Explorar Autores
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl font-bold text-[#B8860B] mb-2">15.000+</div>
                    <div class="text-lg text-gray-600">Livros Gratuitos</div>
                    <div class="text-sm text-gray-500 mt-1">Disponíveis para download</div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl font-bold text-[#B8860B] mb-2">2.500+</div>
                    <div class="text-lg text-gray-600">Autores</div>
                    <div class="text-sm text-gray-500 mt-1">De todo o mundo</div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                    <div class="text-3xl font-bold text-[#B8860B] mb-2">50+</div>
                    <div class="text-lg text-gray-600">Idiomas</div>
                    <div class="text-sm text-gray-500 mt-1">Múltiplos formatos</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Books Section -->
@if(isset($featuredBooks) && $featuredBooks->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-[#333333] mb-4">Livros em Destaque</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Descubra os livros em domínio público mais populares, cuidadosamente selecionados por sua relevância literária e apelo duradouro.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($featuredBooks as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
        <div class="text-center">
            <a href="{{ route('livros.index') }}" class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-6 py-3 text-lg rounded-lg">
                <i class="ri-arrow-right-line mr-2"></i>Ver Todos os Livros
            </a>
        </div>
    </div>
</section>
@endif

<!-- Categories Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-[#333333] mb-4">Explorar por Categoria</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Descubra livros organizados por gênero e assunto. Dos clássicos atemporais aos temas especializados, encontre exatamente o que você procura.
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-book-open-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Literatura Clássica</h3>
                <p class="text-[#B8860B] font-medium">1.250 livros</p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-search-eye-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Mistério &amp; Detetive</h3>
                <p class="text-[#B8860B] font-medium">890 livros</p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-heart-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Romance</h3>
                <p class="text-[#B8860B] font-medium">760 livros</p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-compass-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Aventura</h3>
                <p class="text-[#B8860B] font-medium">650 livros</p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-rocket-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Ficção Científica</h3>
                <p class="text-[#B8860B] font-medium">420 livros</p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-lightbulb-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Filosofia</h3>
                <p class="text-[#B8860B] font-medium">380 livros</p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-time-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">História</h3>
                <p class="text-[#B8860B] font-medium">340 livros</p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors">
                    <i class="ri-quill-pen-line text-2xl text-[#004D40]"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Poesia</h3>
                <p class="text-[#B8860B] font-medium">290 livros</p>
            </div>
        </div>
    </div>
</section>

<!-- Authors Section -->
<section class="py-16 bg-[#FDFBF6]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-[#333333] mb-4">Autores Renomados</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Explore a vida e obra dos escritores mais celebrados da história, com biografias detalhadas e coleções completas de suas obras em domínio público.
            </p>
        </div>
        @if(isset($featuredAuthors) && $featuredAuthors->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($featuredAuthors as $author)
                    <x-author-card :author="$author" />
                @endforeach
            </div>
        @endif
        <div class="text-center">
            <a href="{{ route('autores.index') }}" class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-6 py-3 text-lg rounded-lg">
                <i class="ri-team-line mr-2"></i>Explorar Todos os Autores
            </a>
        </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-[#FDFBF6]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-[#333333] mb-4">Por Que Escolher o Leia Livre?</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Somos mais do que apenas uma biblioteca digital. Oferecemos contexto rico, informações detalhadas e uma experiência de leitura perfeita para entusiastas da literatura.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300">
                    <i class="ri-download-cloud-line text-2xl text-[#004D40] group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Múltiplos Formatos</h3>
                <p class="text-gray-600 leading-relaxed">
                    Baixe livros em formatos PDF, EPUB, TXT, HTML e MOBI. Escolha o formato que melhor funciona para seu dispositivo de leitura e preferências.
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300">
                    <i class="ri-user-line text-2xl text-[#004D40] group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Informações Ricas sobre Autores</h3>
                <p class="text-gray-600 leading-relaxed">
                    Descubra biografias detalhadas, contexto histórico e bibliografias completas de milhares de autores de todo o mundo.
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300">
                    <i class="ri-search-line text-2xl text-[#004D40] group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Busca Avançada</h3>
                <p class="text-gray-600 leading-relaxed">
                    Encontre livros por título, autor, gênero, ano de publicação ou até mesmo temas específicos. Nossa poderosa busca ajuda você a descobrir novos favoritos.
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300">
                    <i class="ri-bookmark-line text-2xl text-[#004D40] group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Biblioteca Pessoal</h3>
                <p class="text-gray-600 leading-relaxed">
                    Crie sua própria biblioteca digital, marque favoritos e acompanhe seu progresso de leitura em todos os seus dispositivos.
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300">
                    <i class="ri-global-line text-2xl text-[#004D40] group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Coleção Multilíngue</h3>
                <p class="text-gray-600 leading-relaxed">
                    Acesse livros em mais de 50 idiomas, desde clássicos em inglês até obras em francês, alemão, espanhol, italiano e muito mais.
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300">
                    <i class="ri-shield-check-line text-2xl text-[#004D40] group-hover:text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Completamente Gratuito</h3>
                <p class="text-gray-600 leading-relaxed">
                    Todo o conteúdo está em domínio público e é completamente gratuito para download, compartilhamento e uso. Sem necessidade de registro, sem taxas ocultas.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-16 bg-[#004D40]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-8">
            <i class="ri-mail-line text-5xl text-[#B8860B] mb-6"></i>
            <h2 class="text-4xl font-bold text-white mb-4">Mantenha-se Atualizado</h2>
            <p class="text-xl text-gray-300 leading-relaxed">
                Receba notificações sobre novos livros adicionados, coleções em destaque e descobertas literárias. Junte-se a milhares de amantes de livros em nossa comunidade.
            </p>
        </div>
        <form class="max-w-md mx-auto" id="newsletter-subscription">
            <div class="flex flex-col sm:flex-row gap-4">
                <input 
                    placeholder="Digite seu endereço de e-mail" 
                    required 
                    class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#B8860B] focus:border-transparent text-[#333333]" 
                    type="email" 
                    name="email"
                >
                <button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#B8860B] text-white hover:bg-[#DAA520] focus:ring-2 focus:ring-[#B8860B]/20 px-6 py-3 text-lg rounded-lg" type="submit">
                    <i class="ri-send-plane-line mr-2"></i>Inscrever-se
                </button>
            </div>
            <p class="text-gray-400 text-sm mt-4">Respeitamos sua privacidade. Você pode cancelar a inscrição a qualquer momento.</p>
        </form>
    </div>
</section>
@endsection
