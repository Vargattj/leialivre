<footer class="bg-[#004D40] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-2xl font-bold mb-4" style="font-family: Pacifico, serif;">Leia Livre</h3>
                <p class="text-gray-300 mb-6 max-w-md">Sua porta de entrada para as maiores obras literárias do mundo. Descubra, baixe e explore milhares de livros em domínio público com rico contexto histórico e informações detalhadas sobre os autores.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="ri-facebook-fill text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="ri-twitter-fill text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="ri-instagram-line text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors">
                        <i class="ri-github-fill text-xl"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Explorar</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('livros.index') }}" class="text-gray-300 hover:text-white transition-colors">Explorar Livros</a></li>
                    <li><a href="{{ route('autores.index') }}" class="text-gray-300 hover:text-white transition-colors">Autores Populares</a></li>
                    <li><a href="{{ route('livros.mais-baixados') }}" class="text-gray-300 hover:text-white transition-colors">Novos Lançamentos</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Coleções</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Livro Aleatório</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Recursos</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Sobre Domínio Público</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Formatos de Download</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Guia de Leitura</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Documentação da API</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contato</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-600 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-300 text-sm">© {{ date('Y') }} Leia Livre. Todo o conteúdo em domínio público está disponível gratuitamente para todos.</p>
            <div class="mt-4 md:mt-0">
                <a href="https://readdy.ai/?origin=logo" class="text-gray-300 hover:text-white text-sm transition-colors" rel="nofollow">Website Builder</a>
            </div>
        </div>
    </div>
</footer>

