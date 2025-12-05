@extends('layouts.admin')

@section('title', 'Gerenciar Livros')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Livros</h1>
    <a href="{{ route('admin.books.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        <i class="ri-add-line mr-1"></i> Novo Livro
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Search & Filter -->
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <form action="{{ route('admin.books.index') }}" method="GET" class="flex flex-col md:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Buscar por título..." 
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            
            <select name="category" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                <option value="">Todas as Categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Filtrar
            </button>
            
            @if(request('search') || request('category'))
                <a href="{{ route('admin.books.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md flex items-center justify-center">
                    Limpar
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título / Autor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arquivos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($books as $book)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex-shrink-0 h-16 w-12">
                            @if($book->cover_url)
                                <img class="h-16 w-12 object-cover rounded shadow-sm" src="{{ $book->cover_url }}" alt="{{ $book->title }}">
                            @else
                                <div class="h-16 w-12 bg-gray-200 flex items-center justify-center text-gray-400 rounded">
                                    <i class="ri-book-line text-xl"></i>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $book->title }}</div>
                        <div class="text-sm text-gray-500">{{ $book->authors_names }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $book->publication_year }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($book->primaryCategory)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $book->primaryCategory->name }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex gap-1">
                            @foreach($book->activeFiles as $file)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200" title="{{ $file->file_url }}">
                                    {{ $file->format }}
                                </span>
                            @endforeach
                            @if($book->activeFiles->isEmpty())
                                <span class="text-red-400 text-xs">Sem arquivos</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.books.edit', $book) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este livro?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Nenhum livro encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection
