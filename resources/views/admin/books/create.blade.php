@extends('layouts.admin')

@section('title', 'Novo Livro')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Novo Livro</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.books._form')
    </form>
</div>
@endsection
