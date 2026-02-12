@extends('layouts.admin')

@section('title', 'Importação JSON')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Importação de Livros via JSON</h1>
        <p class="text-gray-600 mt-1">Cole um array JSON com os dados dos livros para importar em massa.</p>
    </div>

    <div x-data="jsonImporter()" class="space-y-6">
        {{-- Formulário Principal --}}
        <form action="{{ route('admin.import-json.import') }}" method="POST" @submit="handleSubmit">
            @csrf

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 space-y-6">
                    {{-- JSON Input --}}
                    <div>
                        <label for="json_data" class="block text-sm font-medium text-gray-700 mb-2">
                            Dados JSON <span class="text-red-500">*</span>
                        </label>
                        <textarea name="json_data" id="json_data" rows="12" x-model="jsonData"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                            placeholder='[
      {
        "title": "Dom Casmurro",
        "subtitle": null,
        "publication_year": 1899,
        "synopsis": "A história de Bentinho e Capitu...",
        "pages": 256
      },
      {
        "title": "Memórias Póstumas de Brás Cubas",
        "publication_year": 1881
      }
    ]'>{{ old('json_data') }}</textarea>
                        @error('json_data')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">
                            Campos aceitos: title, subtitle, original_title, publication_year, original_publisher,
                            original_language, synopsis, full_description, isbn, pages, is_public_domain,
                            public_domain_year, cover_url, cover_thumbnail_url
                        </p>
                    </div>

                    {{-- Author and Category Selection --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Author --}}
                        <div>
                            <label for="author_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Autor <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select name="author_id" id="author_id" x-model="authorId"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione um autor...</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                    <template x-for="author in newAuthors" :key="author.id">
                                        <option :value="author.id" x-text="author.name + ' (novo)'"></option>
                                    </template>
                                </select>
                                <button type="button" @click="showAuthorModal = true"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                                    title="Criar novo autor">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                            @error('author_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Categoria <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select name="category_id" id="category_id" x-model="categoryId"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione uma categoria...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                    <template x-for="category in newCategories" :key="category.id">
                                        <option :value="category.id" x-text="category.name + ' (novo)'"></option>
                                    </template>
                                </select>
                                <button type="button" @click="showCategoryModal = true"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                                    title="Criar nova categoria">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Preview Button --}}
                    <div class="flex gap-3">
                        <button type="button" @click="previewJson" :disabled="!jsonData || isLoading"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <i class="ri-eye-line mr-1"></i>
                            <span x-text="isLoading ? 'Processando...' : 'Pré-visualizar'"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Preview Table --}}
            <div x-show="previewBooks.length > 0" x-cloak class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="ri-list-check-2 mr-2"></i>
                        Pré-visualização
                        <span class="text-sm font-normal text-gray-600"
                            x-text="'(' + previewBooks.length + ' livros encontrados)'"></span>
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subtítulo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ano</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Páginas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ISBN</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(book, index) in previewBooks" :key="index">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        x-text="book.title || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        x-text="book.subtitle || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        x-text="book.publication_year || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        x-text="book.pages || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="book.isbn || '-'">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Preview Errors --}}
                <template x-if="previewErrors.length > 0">
                    <div class="px-6 py-4 bg-yellow-50 border-t border-yellow-200">
                        <p class="text-sm font-medium text-yellow-800 mb-2">
                            <i class="ri-error-warning-line mr-1"></i> Avisos:
                        </p>
                        <ul class="list-disc list-inside text-sm text-yellow-700">
                            <template x-for="error in previewErrors" :key="error">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>
                </template>

                {{-- Import Button --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="submit" :disabled="!authorId || !categoryId"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="ri-download-line mr-1"></i>
                        Importar <span x-text="previewBooks.length"></span> Livro(s)
                    </button>
                    <p x-show="!authorId || !categoryId" class="mt-2 text-sm text-red-600">
                        Selecione um autor e uma categoria antes de importar.
                    </p>
                </div>
            </div>
        </form>

        {{-- Modal: Create Author --}}
        <div x-show="showAuthorModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showAuthorModal = false"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-10">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="ri-user-add-line mr-2"></i>Novo Autor
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                            <input type="text" x-model="newAuthorName"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nome do autor">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nacionalidade</label>
                            <input type="text" x-model="newAuthorNationality"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Brasil">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showAuthorModal = false"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="createAuthor" :disabled="!newAuthorName || isCreatingAuthor"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                            <span x-text="isCreatingAuthor ? 'Criando...' : 'Criar Autor'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal: Create Category --}}
        <div x-show="showCategoryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showCategoryModal = false">
                </div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-10">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="ri-folder-add-line mr-2"></i>Nova Categoria
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                            <input type="text" x-model="newCategoryName"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nome da categoria">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showCategoryModal = false"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="createCategory" :disabled="!newCategoryName || isCreatingCategory"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                            <span x-text="isCreatingCategory ? 'Criando...' : 'Criar Categoria'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Instructions Card --}}
    <div class="mt-6 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="ri-information-line mr-2"></i>Como usar
        </h3>
        <ol class="list-decimal list-inside text-sm text-blue-700 space-y-2">
            <li>Cole o JSON com os dados dos livros no campo acima (array ou objeto único).</li>
            <li>Selecione ou crie um <strong>autor</strong> que será vinculado a todos os livros importados.</li>
            <li>Selecione ou crie uma <strong>categoria</strong> que será vinculada a todos os livros.</li>
            <li>Clique em <strong>Pré-visualizar</strong> para conferir os dados antes de importar.</li>
            <li>Se tudo estiver correto, clique em <strong>Importar</strong> para adicionar os livros ao sistema.</li>
        </ol>
        <div class="mt-4 p-3 bg-white rounded border border-blue-200">
            <p class="text-sm font-medium text-blue-800 mb-2">Exemplo de JSON:</p>
            <pre class="text-xs text-blue-700 overflow-x-auto"><code>[
      {
        "title": "Dom Casmurro",
        "publication_year": 1899,
        "synopsis": "Romance de Machado de Assis...",
        "pages": 256,
        "is_public_domain": true
      }
    ]</code></pre>
        </div>
    </div>

    <script>
        function jsonImporter() {
            return {
                jsonData: '',
                authorId: '{{ old('author_id') }}',
                categoryId: '{{ old('category_id') }}',
                previewBooks: [],
                previewErrors: [],
                isLoading: false,

                // New author/category
                showAuthorModal: false,
                showCategoryModal: false,
                newAuthorName: '',
                newAuthorNationality: 'Brasil',
                newCategoryName: '',
                isCreatingAuthor: false,
                isCreatingCategory: false,
                newAuthors: [],
                newCategories: [],

                async previewJson() {
                    if (!this.jsonData) return;

                    this.isLoading = true;
                    this.previewBooks = [];
                    this.previewErrors = [];

                    try {
                        const response = await fetch('{{ route('admin.import-json.preview') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ json_data: this.jsonData }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.previewBooks = data.books;
                            this.previewErrors = data.errors || [];
                        } else {
                            alert(data.message || 'Erro ao processar JSON.');
                        }
                    } catch (error) {
                        alert('Erro de conexão. Tente novamente.');
                        console.error(error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                async createAuthor() {
                    if (!this.newAuthorName) return;

                    this.isCreatingAuthor = true;

                    try {
                        const response = await fetch('{{ route('admin.import-json.create-author') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                name: this.newAuthorName,
                                nationality: this.newAuthorNationality,
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.newAuthors.push(data.author);
                            this.authorId = data.author.id;
                            this.showAuthorModal = false;
                            this.newAuthorName = '';
                            this.newAuthorNationality = 'Brasil';
                        } else {
                            alert(data.message || 'Erro ao criar autor.');
                        }
                    } catch (error) {
                        alert('Erro de conexão. Tente novamente.');
                        console.error(error);
                    } finally {
                        this.isCreatingAuthor = false;
                    }
                },

                async createCategory() {
                    if (!this.newCategoryName) return;

                    this.isCreatingCategory = true;

                    try {
                        const response = await fetch('{{ route('admin.import-json.create-category') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ name: this.newCategoryName }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.newCategories.push(data.category);
                            this.categoryId = data.category.id;
                            this.showCategoryModal = false;
                            this.newCategoryName = '';
                        } else {
                            alert(data.message || 'Erro ao criar categoria.');
                        }
                    } catch (error) {
                        alert('Erro de conexão. Tente novamente.');
                        console.error(error);
                    } finally {
                        this.isCreatingCategory = false;
                    }
                },

                handleSubmit(e) {
                    if (!this.authorId || !this.categoryId) {
                        e.preventDefault();
                        alert('Selecione um autor e uma categoria antes de importar.');
                        return false;
                    }
                    return true;
                }
            };
        }
    </script>
@endsection