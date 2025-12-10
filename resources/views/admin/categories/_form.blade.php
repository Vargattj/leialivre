<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nome -->
    <div class="col-span-2 md:col-span-1">
        <label for="name" class="block text-sm font-medium text-gray-700">Nome da Categoria</label>
        <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Categoria Pai -->
    <div class="col-span-2 md:col-span-1">
        <label for="parent_category_id" class="block text-sm font-medium text-gray-700">Categoria Pai (opcional)</label>
        <select name="parent_category_id" id="parent_category_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
            <option value="">Nenhuma (Categoria Principal)</option>
            @foreach($parentCategories as $parent)
                <option value="{{ $parent->id }}" 
                    {{ old('parent_category_id', $category->parent_category_id ?? '') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Deixe vazio para criar uma categoria principal</p>
    </div>

    <!-- Ordem de Exibição -->
    <div class="col-span-2 md:col-span-1">
        <label for="display_order" class="block text-sm font-medium text-gray-700">Ordem de Exibição</label>
        <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $category->display_order ?? 0) }}" min="0"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
        <p class="mt-1 text-xs text-gray-500">Menor número aparece primeiro</p>
    </div>

    <!-- Descrição -->
    <div class="col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea name="description" id="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">{{ old('description', $category->description ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex justify-end">
    <a href="{{ route('admin.categories.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
        Cancelar
    </a>
    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Salvar
    </button>
</div>
