@extends('layouts.admin')

@section('title', 'Editar Livro')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Livro: {{ $book->title }}</h1>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden p-6 mb-6">
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.books._form')
        </form>
    </div>

    <!-- FAQs Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Perguntas Frequentes (FAQs)</h2>
            <button type="button" onclick="toggleFaqForm()"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                <span id="toggleFaqBtnText">+ Nova FAQ</span>
            </button>
        </div>

        <!-- Create FAQ Form (Hidden by default) -->
        <div id="createFaqForm" class="hidden mb-6 p-4 bg-gray-50 rounded border border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Adicionar Nova FAQ</h3>

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.books.faqs.store', $book) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="new_question" class="block text-sm font-medium text-gray-700 mb-2">Pergunta</label>
                    <input type="text" name="question" id="new_question" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Digite a pergunta...">
                </div>
                <div class="mb-4">
                    <label for="new_answer" class="block text-sm font-medium text-gray-700 mb-2">Resposta</label>
                    <textarea name="answer" id="new_answer" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Digite a resposta..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="new_order" class="block text-sm font-medium text-gray-700 mb-2">Ordem</label>
                        <input type="number" name="order" id="new_order" min="0" value="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center pt-6">
                        <input type="checkbox" name="is_active" id="new_is_active" checked
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="new_is_active" class="ml-2 text-sm font-medium text-gray-700">Ativa</label>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Salvar FAQ
                    </button>
                    <button type="button" onclick="toggleFaqForm()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- FAQs List -->
        @if($book->faqs->count() > 0)
            <div class="space-y-4">
                @foreach($book->faqs()->ordered()->get() as $faq)
                    <div class="border border-gray-200 rounded p-4 hover:bg-gray-50 transition" id="faq-{{ $faq->id }}">
                        <!-- View Mode -->
                        <div class="faq-view-{{ $faq->id }}">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-semibold text-gray-800">{{ $faq->question }}</h4>
                                        @if(!$faq->is_active)
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Inativa</span>
                                        @endif
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Ordem:
                                            {{ $faq->order }}</span>
                                    </div>
                                    <p class="text-gray-600 text-sm">{{ $faq->answer }}</p>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <button type="button" onclick="toggleEditFaq({{ $faq->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                        Editar
                                    </button>
                                    <form action="{{ route('admin.books.faqs.destroy', [$book, $faq]) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta FAQ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Mode (Hidden by default) -->
                        <div class="faq-edit-{{ $faq->id }} hidden">
                            <form action="{{ route('admin.books.faqs.update', [$book, $faq]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pergunta</label>
                                    <input type="text" name="question" value="{{ $faq->question }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Resposta</label>
                                    <textarea name="answer" rows="3" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $faq->answer }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                                        <input type="number" name="order" value="{{ $faq->order }}" min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="flex items-center pt-6">
                                        <input type="checkbox" name="is_active" {{ $faq->is_active ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <label class="ml-2 text-sm font-medium text-gray-700">Ativa</label>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-sm">
                                        Salvar
                                    </button>
                                    <button type="button" onclick="toggleEditFaq({{ $faq->id }})"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>Nenhuma FAQ cadastrada ainda.</p>
                <p class="text-sm">Clique em "+ Nova FAQ" para adicionar a primeira pergunta.</p>
            </div>
        @endif
    </div>

    <script>
        function toggleFaqForm() {
            const form = document.getElementById('createFaqForm');
            const btnText = document.getElementById('toggleFaqBtnText');

            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                btnText.textContent = '- Cancelar';
            } else {
                form.classList.add('hidden');
                btnText.textContent = '+ Nova FAQ';
                // Reset form
                form.querySelector('form').reset();
            }
        }

        function toggleEditFaq(faqId) {
            const viewMode = document.querySelector(`.faq-view-${faqId}`);
            const editMode = document.querySelector(`.faq-edit-${faqId}`);

            if (viewMode.classList.contains('hidden')) {
                viewMode.classList.remove('hidden');
                editMode.classList.add('hidden');
            } else {
                viewMode.classList.add('hidden');
                editMode.classList.remove('hidden');
            }
        }

        // Auto-open form if there are validation errors
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('createFaqForm');
                const btnText = document.getElementById('toggleFaqBtnText');
                form.classList.remove('hidden');
                btnText.textContent = '- Cancelar';
            });
        @endif
    </script>
@endsection