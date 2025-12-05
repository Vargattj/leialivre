@extends('layouts.admin')

@section('title', 'Gerenciar Autores')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Autores</h1>
    <a href="{{ route('admin.authors.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        <i class="ri-add-line mr-1"></i> Novo Autor
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Search -->
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <form action="{{ route('admin.authors.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Buscar por nome ou biografia..." 
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Buscar
            </button>
            @if(request('search'))
                <a href="{{ route('admin.authors.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md flex items-center">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nacionalidade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livros</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($authors as $author)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($author->photo_url)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $author->photo_url }}" alt="{{ $author->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        <i class="ri-user-line"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $author->name }}</div>
                                <div class="text-sm text-gray-500">{{ $author->birth_date?->format('Y') ?? '?' }} - {{ $author->death_date?->format('Y') ?? '?' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $author->nationality ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $author->books_count ?? $author->books()->count() }} livros
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.authors.edit', $author) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="{{ route('admin.authors.destroy', $author) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este autor?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Nenhum autor encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($authors->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $authors->links() }}
        </div>
    @endif
</div>
@endsection
