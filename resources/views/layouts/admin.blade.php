<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Leia Livre</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
             class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
             class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
            
            <div class="flex items-center justify-center mt-8">
                <div class="flex items-center">
                    <span class="text-white text-2xl font-['Pacifico']">Leia Livre</span>
                </div>
            </div>

            <nav class="mt-10">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-6 py-2 mt-4 text-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 bg-opacity-25 border-l-4 border-blue-500' : 'hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}">
                    <i class="ri-dashboard-line text-xl"></i>
                    <span class="mx-3">Dashboard</span>
                </a>

                <a href="{{ route('admin.authors.index') }}" 
                   class="flex items-center px-6 py-2 mt-4 text-gray-100 {{ request()->routeIs('admin.authors.*') ? 'bg-gray-700 bg-opacity-25 border-l-4 border-blue-500' : 'hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}">
                    <i class="ri-quill-pen-line text-xl"></i>
                    <span class="mx-3">Autores</span>
                </a>

                <a href="{{ route('admin.books.index') }}" 
                   class="flex items-center px-6 py-2 mt-4 text-gray-100 {{ request()->routeIs('admin.books.*') ? 'bg-gray-700 bg-opacity-25 border-l-4 border-blue-500' : 'hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}">
                    <i class="ri-book-2-line text-xl"></i>
                    <span class="mx-3">Livros</span>
                </a>

                <a href="{{ route('admin.import.index') }}" 
                   class="flex items-center px-6 py-2 mt-4 text-gray-100 {{ request()->routeIs('admin.import.*') ? 'bg-gray-700 bg-opacity-25 border-l-4 border-blue-500' : 'hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100' }}">
                    <i class="ri-download-cloud-2-line text-xl"></i>
                    <span class="mx-3">Importação</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-blue-600">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                        <i class="ri-menu-line text-2xl"></i>
                    </button>
                </div>

                <div class="flex items-center">
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" 
                                class="relative block h-8 w-8 rounded-full overflow-hidden shadow focus:outline-none">
                            <img class="h-full w-full object-cover" 
                                 src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=0D8ABC&color=fff" 
                                 alt="Avatar">
                        </button>

                        <div x-show="dropdownOpen" @click="dropdownOpen = false" x-cloak
                             class="fixed inset-0 h-full w-full z-10"></div>

                        <div x-show="dropdownOpen" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-20">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-600 hover:text-white">Perfil</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-600 hover:text-white">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded text-green-800 shadow-sm flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="ri-checkbox-circle-line text-xl mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-800 shadow-sm flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="ri-error-warning-line text-xl mr-2"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
