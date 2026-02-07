@extends('layouts.admin')

@section('title', 'Nova Citação')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Nova Citação</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.quotes.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="book_id" class="block text-gray-700 text-sm font-bold mb-2">
                    Livro <span class="text-red-500">*</span>
                </label>
                <select name="book_id" id="book_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('book_id') border-red-500 @enderror"
                    required>
                    <option value="">Selecione um livro</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                            {{ $book->title }}
                        </option>
                    @endforeach
                </select>
                @error('book_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="author_id" class="block text-gray-700 text-sm font-bold mb-2">
                    Autor <span class="text-red-500">*</span>
                </label>
                <select name="author_id" id="author_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('author_id') border-red-500 @enderror"
                    required>
                    <option value="">Selecione um autor</option>
                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                            {{ $author->name }}
                        </option>
                    @endforeach
                </select>
                @error('author_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="text" class="block text-gray-700 text-sm font-bold mb-2">
                    Texto da Citação <span class="text-red-500">*</span>
                </label>
                <textarea name="text" id="text" rows="4"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('text') border-red-500 @enderror"
                    required>{{ old('text') }}</textarea>
                @error('text')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="page_number" class="block text-gray-700 text-sm font-bold mb-2">
                    Número da Página (opcional)
                </label>
                <input type="text" name="page_number" id="page_number" value="{{ old('page_number') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('page_number') border-red-500 @enderror">
                @error('page_number')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="order" class="block text-gray-700 text-sm font-bold mb-2">
                    Ordem de Exibição
                </label>
                <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('order') border-red-500 @enderror">
                @error('order')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700">Citação ativa</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.quotes.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Criar Citação
                </button>
            </div>
        </form>
    </div>
@endsection