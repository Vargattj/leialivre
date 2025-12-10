@extends('layouts.admin')

@section('title', 'Nova Categoria')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Nova Categoria</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('admin.categories._form')
    </form>
</div>
@endsection
