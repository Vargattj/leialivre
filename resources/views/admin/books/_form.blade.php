<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Título -->
    <div class="col-span-2 md:col-span-1">
        <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
        <input type="text" name="title" id="title" value="{{ old('title', $book->title ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Subtítulo -->
    <div class="col-span-2 md:col-span-1">
        <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtítulo</label>
        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $book->subtitle ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
    </div>

    <!-- Ano de Publicação -->
    <div class="col-span-2 md:col-span-1">
        <label for="publication_year" class="block text-sm font-medium text-gray-700">Ano de Publicação</label>
        <input type="number" name="publication_year" id="publication_year" value="{{ old('publication_year', $book->publication_year ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
    </div>

    <!-- Autores -->
    <div class="col-span-2 md:col-span-1">
        <label for="authors" class="block text-sm font-medium text-gray-700">Autores</label>
        <select name="authors[]" id="authors" multiple required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2 h-32">
            @foreach($authors as $author)
                <option value="{{ $author->id }}" 
                    {{ (collect(old('authors', isset($book) ? $book->authors->pluck('id') : []))->contains($author->id)) ? 'selected' : '' }}>
                    {{ $author->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Segure Ctrl (Windows) ou Cmd (Mac) para selecionar vários.</p>
    </div>

    <!-- Categorias -->
    <div class="col-span-2 md:col-span-1">
        <label for="categories" class="block text-sm font-medium text-gray-700">Categorias</label>
        <select name="categories[]" id="categories" multiple required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2 h-32">
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ (collect(old('categories', isset($book) ? $book->categories->pluck('id') : []))->contains($category->id)) ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">A primeira selecionada será a categoria principal.</p>
    </div>

    <!-- Sinopse -->
    <div class="col-span-2">
        <label for="synopsis" class="block text-sm font-medium text-gray-700">Sinopse</label>
        <textarea name="synopsis" id="synopsis" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">{{ old('synopsis', $book->synopsis ?? '') }}</textarea>
    </div>

    <!-- Capa -->
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700">Capa do Livro</label>
        <div class="mt-1 flex items-center space-x-5">
            <span class="inline-block h-24 w-16 overflow-hidden bg-gray-100 border rounded">
                @if(isset($book) && $book->cover_url)
                    <img src="{{ $book->cover_url }}" alt="Capa atual" class="h-full w-full object-cover">
                @else
                    <div class="h-full w-full flex items-center justify-center text-gray-300">
                        <i class="ri-image-line text-2xl"></i>
                    </div>
                @endif
            </span>
            <input type="file" name="cover" accept="image/*"
                class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        </div>
    </div>

    <!-- Arquivos (Downloads) -->
    <div class="col-span-2 border-t pt-6 mt-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Arquivos de Download</h3>
        
        @if(isset($book) && $book->files->count() > 0)
            <div class="mb-4 space-y-2">
                <p class="text-sm font-medium text-gray-700">Arquivos Existentes:</p>
                @foreach($book->files as $file)
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded border">
                        <div class="flex items-center">
                            <span class="font-bold text-gray-700 mr-3">{{ $file->format }}</span>
                            <a href="{{ $file->file_url }}" target="_blank" class="text-blue-600 hover:underline text-sm truncate max-w-xs">
                                {{ $file->file_url }}
                            </a>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="delete_files[]" value="{{ $file->id }}" class="mr-2 text-red-600 focus:ring-red-500">
                            <span class="text-sm text-red-600">Excluir</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="space-y-4" x-data="{ files: [{id: 1}] }">
            <p class="text-sm font-medium text-gray-700">Adicionar Novos Arquivos:</p>
            
            <template x-for="(file, index) in files" :key="file.id">
                <div class="flex gap-4 items-start bg-gray-50 p-3 rounded border border-dashed border-gray-300">
                    <div class="w-1/4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Formato</label>
                        <select :name="`{{ isset($book) ? 'new_files' : 'files_data' }}[${index}][format]`" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                            <option value="">Selecione...</option>
                            <option value="PDF">PDF</option>
                            <option value="EPUB">EPUB</option>
                            <option value="MOBI">MOBI</option>
                            <option value="TXT">TXT</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">URL Externa de Download</label>
                        <input type="url" :name="`{{ isset($book) ? 'new_files' : 'files_data' }}[${index}][url]`" placeholder="https://exemplo.com/livro.pdf"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    </div>
                    <div class="pt-6">
                        <button type="button" @click="files = files.filter(f => f.id !== file.id)" class="text-red-500 hover:text-red-700" x-show="files.length > 1">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" @click="files.push({id: Date.now()})" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                <i class="ri-add-circle-line mr-1"></i> Adicionar outro arquivo
            </button>
        </div>
    </div>
</div>

<div class="mt-8 flex justify-end border-t pt-4">
    <a href="{{ route('admin.books.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
        Cancelar
    </a>
    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Salvar Livro
    </button>
</div>
