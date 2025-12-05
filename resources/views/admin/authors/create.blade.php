@extends('layouts.admin')

@section('title', 'Novo Autor')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Novo Autor</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden p-6">
    <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.authors._form')
    </form>
</div>
@endsection
