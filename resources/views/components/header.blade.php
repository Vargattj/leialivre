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
                <button id="mobile-menu-button" class="text-[#333333] hover:text-[#004D40] p-2">
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

    <!-- Mobile Menu Overlay & Drawer -->
    <div id="mobile-menu" class="fixed inset-0 z-[60] md:hidden hidden" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div id="mobile-menu-backdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>
        
        <div id="mobile-menu-container" class="fixed inset-y-0 left-0 z-[70] w-full max-w-xs overflow-y-auto bg-white px-6 py-6 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div class="flex items-center justify-between">
                <a href="/" class="-m-1.5 p-1.5">
                    <h1 class="w-24" style="font-family: Pacifico, serif;">
                        <img src="{{ asset('images/logo3.png') }}" alt="Leia Livre" class="w-full h-full">
                    </h1>
                </a>
                <button id="close-mobile-menu" type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700 hover:text-[#004D40] transition-colors">
                    <span class="sr-only">Fechar menu</span>
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <div class="mt-8 flow-root">
                <div class="-my-6 divide-y divide-gray-100">
                    <div class="space-y-4 py-6">
                        <a href="{{ route('livros.index') }}" class="flex items-center gap-3 -mx-3 rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 transition-colors">
                            <i class="ri-explore-line text-[#004D40]"></i>
                            Explorar
                        </a>
                        <a href="{{ route('categorias.index') }}" class="flex items-center gap-3 -mx-3 rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 transition-colors">
                            <i class="ri-grid-line text-[#004D40]"></i>
                            Categorias
                        </a>
                        <a href="{{ route('autores.index') }}" class="flex items-center gap-3 -mx-3 rounded-lg px-3 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 transition-colors">
                            <i class="ri-user-line text-[#004D40]"></i>
                            Autores
                        </a>
                    </div>
                    <div class="py-6">
                        <form action="{{ route('livros.buscar') }}" method="GET" class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ri-search-line text-gray-400"></i>
                            </div>
                            <input 
                                name="q"
                                placeholder="Buscar livros, autores..." 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent bg-gray-50" 
                                type="text"
                                value="{{ request('q') }}"
                            >
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const closeButton = document.getElementById('close-mobile-menu');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuContainer = document.getElementById('mobile-menu-container');
        const backdrop = document.getElementById('mobile-menu-backdrop');

        function openMenu() {
            mobileMenu.classList.remove('hidden');
            // Force reflow
            mobileMenu.offsetHeight;
            backdrop.classList.add('opacity-100');
            backdrop.classList.remove('opacity-0');
            mobileMenuContainer.classList.add('translate-x-0');
            mobileMenuContainer.classList.remove('-translate-x-full');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            mobileMenuContainer.classList.remove('translate-x-0');
            mobileMenuContainer.classList.add('-translate-x-full');
            document.body.style.overflow = '';
            
            // Wait for transition to finish
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
            }, 300);
        }

        if (menuButton) {
            menuButton.addEventListener('click', openMenu);
        }

        if (closeButton) {
            closeButton.addEventListener('click', closeMenu);
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeMenu);
        }
    });
</script>

