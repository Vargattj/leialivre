@extends('layouts.app')

@section('title', 'Sobre o Leia Livre - Livros em Domínio Público')

@section('seo')
    <x-seo-meta
        title="Sobre o Leia Livre - Conheça nossa Missão | Leia Livre"
        description="Saiba mais sobre o Leia Livre, nossa biblioteca digital de livros em domínio público. Conheça nossa missão de democratizar o acesso à literatura clássica."
        keywords="sobre nós, missão, visão, valores, biblioteca digital, democratização da leitura"
    />
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-[#333333]">
                <a href="{{ route('home') }}" class="hover:text-[#004D40] transition-colors">Início</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-[#B8860B] font-medium">Sobre</span>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-[#004D40] to-[#00695C] text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    Sobre o Leia Livre
                </h1>
                <p class="text-xl md:text-2xl text-white/90 leading-relaxed">
                    Difundindo e divulgando a riqueza da literatura brasileira em domínio público, 
                    reunindo informações de diferentes fontes em um único lugar acessível a todos.
                </p>
            </div>
        </div>
    </div>

    <!-- Mission Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-[#004D40] mb-6">Nossa Missão</h2>
                    <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                        O <strong class="text-[#004D40]">Leia Livre</strong> nasceu com o propósito de democratizar o acesso 
                        à literatura brasileira em domínio público. Acreditamos que o conhecimento e a cultura devem ser 
                        livres e acessíveis a todos, sem barreiras financeiras ou geográficas.
                    </p>
                    <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                        Nossa plataforma consolida informações de diversas fontes confiáveis, oferecendo uma experiência 
                        única de descoberta e leitura. Cada livro em nossa biblioteca vem acompanhado de contexto histórico, 
                        biografias detalhadas dos autores e múltiplos formatos de download.
                    </p>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Mais do que uma biblioteca digital, somos um portal que preserva e celebra o patrimônio literário 
                        brasileiro, garantindo que obras clássicas e importantes continuem acessíveis para as gerações futuras.
                    </p>
                </div>
                <div class="bg-gradient-to-br from-[#FDFBF6] to-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#004D40] rounded-lg flex items-center justify-center">
                                <i class="ri-book-open-line text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">Literatura Brasileira</h3>
                                <p class="text-gray-600">Foco especial em obras de autores brasileiros em domínio público</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#004D40] rounded-lg flex items-center justify-center">
                                <i class="ri-global-line text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">Múltiplas Fontes</h3>
                                <p class="text-gray-600">Consolidação de dados de diferentes bibliotecas e repositórios</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-[#004D40] rounded-lg flex items-center justify-center">
                                <i class="ri-download-cloud-2-line text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">Formatos Diversos</h3>
                                <p class="text-gray-600">PDF, EPUB, MOBI e outros formatos para todos os dispositivos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What is Public Domain Section -->
    <section class="py-16 bg-[#FDFBF6]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-[#004D40] mb-4">O Que É Domínio Público?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Entenda o conceito que torna possível o acesso livre e gratuito a obras literárias
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-time-line text-3xl text-[#004D40]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tempo Decorrido</h3>
                    <p class="text-gray-600 leading-relaxed">
                        No Brasil, obras entram em domínio público 70 anos após a morte do autor, 
                        permitindo uso livre e gratuito.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-shield-check-line text-3xl text-[#004D40]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Uso Livre</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Livros em domínio público podem ser copiados, distribuídos, adaptados e 
                        utilizados sem restrições de direitos autorais.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-heart-line text-3xl text-[#004D40]"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Patrimônio Cultural</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Essas obras fazem parte do patrimônio cultural da humanidade e devem 
                        permanecer acessíveis para todos.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-[#004D40] mb-4">Como Funciona</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nossa tecnologia consolida informações de diferentes fontes para oferecer a melhor experiência
                </p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="space-y-8">
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-[#004D40] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            1
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Coleta de Dados</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Buscamos informações de livros em domínio público em diversas fontes confiáveis, 
                                incluindo bibliotecas digitais, repositórios acadêmicos e projetos de preservação cultural.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-[#004D40] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            2
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Consolidação e Enriquecimento</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Utilizamos tecnologia avançada para consolidar dados de múltiplas fontes, eliminar 
                                duplicatas e enriquecer cada obra com contexto histórico, biografias detalhadas dos 
                                autores e informações relevantes sobre o período literário.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-[#004D40] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            3
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Organização e Disponibilização</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Organizamos os livros por categorias, autores e temas, facilitando a descoberta. 
                                Cada obra está disponível em múltiplos formatos (PDF, EPUB, MOBI, etc.) para 
                                atender diferentes preferências de leitura.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-[#004D40] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            4
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Acesso Gratuito e Ilimitado</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Todo o conteúdo está disponível gratuitamente, sem necessidade de cadastro ou pagamento. 
                                Você pode baixar, ler e compartilhar quantas vezes quiser, respeitando sempre o domínio público.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-16 bg-gradient-to-br from-[#FDFBF6] to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-[#004D40] mb-4">Nossos Valores</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Os princípios que guiam nosso trabalho diário
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-6 shadow-md border border-gray-100 text-center hover:shadow-lg transition-shadow">
                    <i class="ri-lock-unlock-line text-4xl text-[#004D40] mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Acesso Livre</h3>
                    <p class="text-gray-600 text-sm">
                        Acreditamos que o conhecimento deve ser livre e acessível a todos, sem barreiras.
                    </p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-md border border-gray-100 text-center hover:shadow-lg transition-shadow">
                    <i class="ri-book-mark-line text-4xl text-[#004D40] mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Preservação Cultural</h3>
                    <p class="text-gray-600 text-sm">
                        Trabalhamos para preservar e manter acessível o patrimônio literário brasileiro.
                    </p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-md border border-gray-100 text-center hover:shadow-lg transition-shadow">
                    <i class="ri-team-line text-4xl text-[#004D40] mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Democratização</h3>
                    <p class="text-gray-600 text-sm">
                        Facilitamos o acesso à literatura para pessoas de todas as origens e condições.
                    </p>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-md border border-gray-100 text-center hover:shadow-lg transition-shadow">
                    <i class="ri-information-line text-4xl text-[#004D40] mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Qualidade e Contexto</h3>
                    <p class="text-gray-600 text-sm">
                        Oferecemos informações precisas e contexto histórico para enriquecer a leitura.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-[#004D40] to-[#00695C] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Faça Parte Desta Jornada</h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Explore nossa biblioteca, descubra novos autores e contribua para a preservação da literatura brasileira
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('livros.index') }}" 
                   class="inline-flex items-center justify-center px-8 py-3 bg-white text-[#004D40] rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    <i class="ri-book-open-line mr-2"></i>
                    Explorar Livros
                </a>
                <a href="{{ route('contact.index') }}" 
                   class="inline-flex items-center justify-center px-8 py-3 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10 transition-colors">
                    <i class="ri-mail-line mr-2"></i>
                    Entre em Contato
                </a>
            </div>
        </div>
    </section>
@endsection

