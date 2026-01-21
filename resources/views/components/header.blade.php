<header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <nav class="hidden md:flex items-center space-x-6 w-1/3">
                <a href="{{ route('livros.index') }}" class="text-[#333333] hover:text-[#004D40] font-medium transition-colors">Explorar</a>
                <a href="{{ route('categorias.index') }}" class="text-[#333333] hover:text-[#004D40] font-medium transition-colors">Categorias</a>
                <a href="{{ route('autores.index') }}" class="text-[#333333] hover:text-[#004D40] font-medium transition-colors">Autores</a>
                {{-- <button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md">
                    <i class="ri-bookmark-line mr-2"></i>Minha Biblioteca
                </button> --}}
            </nav>
            <div class="md:hidden">
                <button class="text-[#333333] hover:text-[#004D40] p-2">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
            <div class="flex items-center ml-auto w-full justify-center">
                <a href="/">
                    <h1 class="w-36 mb-2" style="font-family: Pacifico, serif;">
                        <img src="{{ asset('images/logo3.png') }}" alt="Leia Livre" class="w-full h-full">
                    </h1>
                </a>
            </div>
            <div class="hidden md:flex ml-auto max-w-lg mx-8  w-1/3">
                <form class="relative w-full" action="{{ route('livros.buscar') }}" method="GET">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ri-search-line text-gray-400 text-sm"></i>
                    </div>
                    <input 
                        name="q"
                        placeholder="Buscar livros, autores ou assuntos..." 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent" 
                        type="text" 
                        value="{{ request('q') }}"
                    >
                </form>
            </div>
           

        </div>
    </div>
</header>

