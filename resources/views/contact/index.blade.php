@extends('layouts.app')

@section('title', 'Contato - Leia Livre')

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-[#333333]">
                <a href="{{ route('home') }}" class="hover:text-[#004D40] transition-colors">Início</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-[#B8860B] font-medium">Contato</span>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-white to-[#FDFBF6] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-[#004D40] mb-4">Entre em Contato</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Tem alguma dúvida, sugestão ou quer contribuir com o Leia Livre? Estamos aqui para ajudar!
                </p>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Information -->
            <div class="space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-[#004D40] mb-6">Informações de Contato</h2>
                    <p class="text-gray-600 mb-8">
                        O Leia Livre é uma plataforma dedicada a difundir e divulgar livros brasileiros em domínio público, 
                        buscando informações de diferentes fontes e entregando tudo em um só lugar. 
                        Sua opinião e contribuições são muito importantes para nós!
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-[#004D40] rounded-lg flex items-center justify-center">
                            <i class="ri-mail-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                            <p class="text-gray-600">contato@leialivre.com.br</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-[#004D40] rounded-lg flex items-center justify-center">
                            <i class="ri-time-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Horário de Atendimento</h3>
                            <p class="text-gray-600">Segunda a Sexta, das 9h às 18h</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-[#004D40] rounded-lg flex items-center justify-center">
                            <i class="ri-question-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Como Podemos Ajudar?</h3>
                            <p class="text-gray-600">
                                Sugestões de livros, reportar problemas, parcerias ou simplesmente dizer olá!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="pt-6 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-4">Siga-nos nas Redes Sociais</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-[#004D40] rounded-lg flex items-center justify-center text-white hover:bg-[#00695C] transition-colors">
                            <i class="ri-facebook-fill text-xl"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#004D40] rounded-lg flex items-center justify-center text-white hover:bg-[#00695C] transition-colors">
                            <i class="ri-twitter-fill text-xl"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#004D40] rounded-lg flex items-center justify-center text-white hover:bg-[#00695C] transition-colors">
                            <i class="ri-instagram-line text-xl"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#004D40] rounded-lg flex items-center justify-center text-white hover:bg-[#00695C] transition-colors">
                            <i class="ri-github-fill text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-[#004D40] mb-6">Envie sua Mensagem</h2>
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <i class="ri-checkbox-circle-line text-green-600 text-xl"></i>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start space-x-2">
                            <i class="ri-error-warning-line text-red-600 text-xl mt-0.5"></i>
                            <div>
                                <p class="text-red-800 font-semibold mb-2">Por favor, corrija os seguintes erros:</p>
                                <ul class="list-disc list-inside text-red-700 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent transition-colors"
                            placeholder="Seu nome completo"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent transition-colors"
                            placeholder="seu@email.com"
                        >
                    </div>

                    <!-- Assunto -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Assunto <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="subject" 
                            name="subject" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent transition-colors"
                        >
                            <option value="">Selecione um assunto</option>
                            <option value="sugestao" {{ old('subject') == 'sugestao' ? 'selected' : '' }}>Sugestão de Livro</option>
                            <option value="problema" {{ old('subject') == 'problema' ? 'selected' : '' }}>Reportar Problema</option>
                            <option value="parceria" {{ old('subject') == 'parceria' ? 'selected' : '' }}>Parceria</option>
                            <option value="duvida" {{ old('subject') == 'duvida' ? 'selected' : '' }}>Dúvida</option>
                            <option value="outro" {{ old('subject') == 'outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>

                    <!-- Mensagem -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Mensagem <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            required
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent transition-colors resize-none"
                            placeholder="Escreva sua mensagem aqui..."
                        >{{ old('message') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit"
                            class="w-full bg-[#004D40] text-white py-3 px-6 rounded-lg font-semibold hover:bg-[#00695C] focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center space-x-2"
                        >
                            <i class="ri-send-plane-line"></i>
                            <span>Enviar Mensagem</span>
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 text-center">
                        <span class="text-red-500">*</span> Campos obrigatórios
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- FAQ Section (Opcional) -->
    <div class="bg-gray-50 py-6 mt-6 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-[#004D40] mb-4">Perguntas Frequentes</h2>
                <p class="text-gray-600">Encontre respostas rápidas para as dúvidas mais comuns</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="ri-question-line text-[#004D40] mr-2"></i>
                        Como posso sugerir um livro?
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Use o formulário de contato acima selecionando "Sugestão de Livro" no campo assunto. 
                        Inclua o máximo de informações possível sobre o livro.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="ri-question-line text-[#004D40] mr-2"></i>
                        Os livros são realmente gratuitos?
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Sim! Todos os livros disponíveis no Leia Livre estão em domínio público e podem ser 
                        baixados gratuitamente sem custos ou cadastros.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="ri-question-line text-[#004D40] mr-2"></i>
                        Em quais formatos os livros estão disponíveis?
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Oferecemos livros em diversos formatos como PDF, EPUB, MOBI e outros, 
                        dependendo da disponibilidade de cada obra.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="ri-question-line text-[#004D40] mr-2"></i>
                        Como posso contribuir com o projeto?
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Entre em contato conosco através do formulário selecionando "Parceria" ou "Outro". 
                        Adoraríamos ouvir suas ideias!
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

