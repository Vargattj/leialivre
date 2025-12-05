@extends('layouts.admin')

@section('title', 'Editar Livro')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Editar Livro: {{ $book->title }}</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.books._form')
    </form>
</div>
@endsection
