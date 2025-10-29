<!-- ============================================ -->
<!-- resources/views/layouts/app.blade.php -->
<!-- Layout base -->
<!-- ============================================ -->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Livros em Domínio Público</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md mb-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                    📚 Biblioteca Domínio Público
                </a>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('livros.index') }}" class="hover:text-blue-600">Livros</a>
                    <a href="{{ route('autores.index') }}" class="hover:text-blue-600">Autores</a>
                    <a href="{{ route('livros.mais-baixados') }}" class="hover:text-blue-600">Mais Baixados</a>
                    <a href="{{ route('autores.brasileiros') }}" class="hover:text-blue-600">Autores Brasileiros</a>
                </div>

                <!-- Busca rápida -->
                <form action="{{ route('livros.buscar') }}" method="GET" class="hidden md:block">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Buscar..." 
                        class="px-4 py-2 border rounded-lg w-64"
                    >
                </form>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">Sobre o Projeto</h3>
                    <p class="text-gray-300 text-sm">
                        Biblioteca digital com livros brasileiros em domínio público. 
                        Todos os livros são gratuitos e legais para download.
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Links Úteis</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-300 hover:text-white">O que é domínio público?</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Como contribuir</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Contato</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Estatísticas</h3>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li>📚 {{ \App\Models\Book::count() }} livros disponíveis</li>
                        <li>✍️ {{ \App\Models\Author::count() }} autores</li>
                        <li>📥 {{ number_format(\App\Models\Book::sum('total_downloads')) }} downloads</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Biblioteca Domínio Público. Todos os livros são de domínio público.</p>
            </div>
        </div>
    </footer>
</body>
</html>