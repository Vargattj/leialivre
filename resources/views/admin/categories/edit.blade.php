@extends('layouts.admin')

@section('title', 'Editar Categoria')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Editar Categoria: {{ $category->name }}</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
    </form>
</div>
@endsection
