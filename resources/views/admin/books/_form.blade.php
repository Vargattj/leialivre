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
    <div class="col-span-2 md:col-span-1" x-data="{ 
        showAuthorModal: false, 
        newAuthorName: '',
        newAuthorNationality: '',
        isCreatingAuthor: false,
        async createAuthor() {
            if (!this.newAuthorName) return;
            this.isCreatingAuthor = true;
            
            try {
                const response = await fetch('{{ route('admin.authors.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newAuthorName,
                        nationality: this.newAuthorNationality
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    const select = document.getElementById('authors');
                    const option = new Option(data.author.name, data.author.id, true, true);
                    select.add(option);
                    
                    this.newAuthorName = '';
                    this.newAuthorNationality = '';
                    this.showAuthorModal = false;
                    
                    alert('Autor criado com sucesso!');
                } else {
                    alert(data.message || 'Erro ao criar autor');
                }
            } catch (error) {
                alert('Erro ao criar autor: ' + error.message);
            } finally {
                this.isCreatingAuthor = false;
            }
        }
    }">
        <div class="flex items-center justify-between">
            <label for="authors" class="block text-sm font-medium text-gray-700">Autores</label>
            <button type="button" @click="showAuthorModal = true" class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center">
                <i class="ri-add-circle-line mr-1"></i> Novo Autor
            </button>
        </div>
        <select name="authors[]" id="authors" multiple required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2 h-32">
            @foreach($authors as $author)
                <option value="{{ $author->id }}" 
                    {{ (collect(old('authors', isset($book) ? $book->authors->pluck('id') : []))->contains($author->id)) ? 'selected' : '' }}>
                    {{ $author->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Segure Ctrl (Windows) ou Cmd (Mac) para selecionar vários.</p>

        <!-- Modal Autor -->
        <div x-show="showAuthorModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAuthorModal" @click="showAuthorModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showAuthorModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    Novo Autor
                                </h3>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nome do Autor</label>
                                        <input type="text" x-model="newAuthorName" @keydown.enter.prevent="createAuthor()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2" placeholder="Ex: Machado de Assis">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nacionalidade (opcional)</label>
                                        <input type="text" x-model="newAuthorNationality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2" placeholder="Ex: Brasil">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="createAuthor()" :disabled="isCreatingAuthor || !newAuthorName" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isCreatingAuthor">Criar</span>
                            <span x-show="isCreatingAuthor">Criando...</span>
                        </button>
                        <button type="button" @click="showAuthorModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categorias -->
    <div class="col-span-2 md:col-span-1" x-data="{ 
        showModal: false, 
        newCategoryName: '', 
        newCategoryDescription: '',
        isCreating: false,
        async createCategory() {
            if (!this.newCategoryName) return;
            this.isCreating = true;
            
            try {
                const response = await fetch('{{ route('admin.categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newCategoryName,
                        description: this.newCategoryDescription
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Adicionar nova categoria ao select
                    const select = document.getElementById('categories');
                    const option = new Option(data.category.name, data.category.id, true, true);
                    select.add(option);
                    
                    // Limpar e fechar modal
                    this.newCategoryName = '';
                    this.newCategoryDescription = '';
                    this.showModal = false;
                    
                    // Mostrar mensagem de sucesso
                    alert('Categoria criada com sucesso!');
                } else {
                    alert(data.message || 'Erro ao criar categoria');
                }
            } catch (error) {
                alert('Erro ao criar categoria: ' + error.message);
            } finally {
                this.isCreating = false;
            }
        }
    }">
        <div class="flex items-center justify-between">
            <label for="categories" class="block text-sm font-medium text-gray-700">Categorias</label>
            <button type="button" @click="showModal = true" class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center">
                <i class="ri-add-circle-line mr-1"></i> Nova Categoria
            </button>
        </div>
        <select name="categories[]" id="categories" multiple required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2 h-32">
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ (collect(old('categories', isset($book) ? $book->categories->pluck('id') : []))->contains($category->id)) ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">A primeira selecionada será a categoria principal.</p>

        <!-- Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" @click="showModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                    Nova Categoria
                                </h3>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nome da Categoria</label>
                                        <input type="text" x-model="newCategoryName" @keydown.enter.prevent="createCategory()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2" placeholder="Ex: Romance">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Descrição (opcional)</label>
                                        <textarea x-model="newCategoryDescription" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="createCategory()" :disabled="isCreating || !newCategoryName" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isCreating">Criar</span>
                            <span x-show="isCreating">Criando...</span>
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
