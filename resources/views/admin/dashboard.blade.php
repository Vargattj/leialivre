@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium">Dashboard</h3>

    <div class="mt-4">
        <div class="flex flex-wrap -mx-6">
            <div class="w-full px-6 sm:w-1/2 xl:w-1/3">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                    <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75">
                        <i class="ri-book-2-line text-white text-2xl"></i>
                    </div>

                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['books'] }}</h4>
                        <div class="text-gray-500">Livros Cadastrados</div>
                    </div>
                </div>
            </div>

            <div class="w-full px-6 sm:w-1/2 xl:w-1/3 mt-6 sm:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                    <div class="p-3 rounded-full bg-orange-600 bg-opacity-75">
                        <i class="ri-quill-pen-line text-white text-2xl"></i>
                    </div>

                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['authors'] }}</h4>
                        <div class="text-gray-500">Autores</div>
                    </div>
                </div>
            </div>

            <div class="w-full px-6 sm:w-1/2 xl:w-1/3 mt-6 xl:mt-0">
                <div class="flex items-center px-5 py-6 shadow-sm rounded-md bg-white">
                    <div class="p-3 rounded-full bg-green-600 bg-opacity-75">
                        <i class="ri-download-cloud-2-line text-white text-2xl"></i>
                    </div>

                    <div class="mx-5">
                        <h4 class="text-2xl font-semibold text-gray-700">{{ number_format($stats['downloads']) }}</h4>
                        <div class="text-gray-500">Downloads Totais</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white shadow rounded-md p-6">
            <h4 class="text-lg font-semibold text-gray-700 mb-4">Bem-vindo ao Painel Administrativo</h4>
            <p class="text-gray-600">
                Utilize o menu lateral para gerenciar o acervo de livros e autores, ou importar novos conteúdos através das integrações disponíveis.
            </p>
        </div>
    </div>
@endsection
