@extends('layouts.admin')

@section('title', 'Editar Autor')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Editar Autor: {{ $author->name }}</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ route('admin.authors.update', $author) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.authors._form')
    </form>
</div>
@endsection
