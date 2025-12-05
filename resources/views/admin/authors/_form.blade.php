<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nome -->
    <div class="col-span-2 md:col-span-1">
        <label for="name" class="block text-sm font-medium text-gray-700">Nome (Principal)</label>
        <input type="text" name="name" id="name" value="{{ old('name', $author->name ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Nome Completo -->
    <div class="col-span-2 md:col-span-1">
        <label for="full_name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $author->full_name ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
        @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Nacionalidade -->
    <div class="col-span-2 md:col-span-1">
        <label for="nationality" class="block text-sm font-medium text-gray-700">Nacionalidade</label>
        <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $author->nationality ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
    </div>

    <!-- Datas -->
    <div class="col-span-2 md:col-span-1 grid grid-cols-2 gap-4">
        <div>
            <label for="birth_date" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', isset($author) && $author->birth_date ? $author->birth_date->format('Y-m-d') : '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
        </div>
        <div>
            <label for="death_date" class="block text-sm font-medium text-gray-700">Data de Falecimento</label>
            <input type="date" name="death_date" id="death_date" value="{{ old('death_date', isset($author) && $author->death_date ? $author->death_date->format('Y-m-d') : '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
        </div>
    </div>

    <!-- Biografia -->
    <div class="col-span-2">
        <label for="biography" class="block text-sm font-medium text-gray-700">Biografia</label>
        <textarea name="biography" id="biography" rows="5"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">{{ old('biography', $author->biography ?? '') }}</textarea>
    </div>

    <!-- Foto -->
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700">Foto do Autor</label>
        <div class="mt-1 flex items-center space-x-5">
            <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                @if(isset($author) && $author->photo_url)
                    <img src="{{ $author->photo_url }}" alt="Foto atual" class="h-full w-full object-cover">
                @else
                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                @endif
            </span>
            <input type="file" name="photo" accept="image/*"
                class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        </div>
        <p class="mt-2 text-xs text-gray-500">PNG, JPG, GIF até 2MB</p>
    </div>
</div>

<div class="mt-6 flex justify-end">
    <a href="{{ route('admin.authors.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
        Cancelar
    </a>
    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Salvar
    </button>
</div>
