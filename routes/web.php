<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;


// Home page
Route::get('/', [BookController::class, 'featured'])->name('home');

// Books
Route::prefix('livros')->name('livros.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index');
    Route::get('/buscar', [BookController::class, 'search'])->name('buscar');
    Route::get('/mais-baixados', [BookController::class, 'mostDownloaded'])->name('mais-baixados');
    Route::get('/categoria/{slug}', [BookController::class, 'byCategory'])->name('categorias');
    Route::get('/{slug}', [BookController::class, 'show'])->name('show');
});

// Authors
Route::prefix('autores')->name('autores.')->group(function () {
    Route::get('/', [AuthorController::class, 'index'])->name('index');
    Route::get('/brasileiros', [AuthorController::class, 'brazilian'])->name('brasileiros');
    Route::get('/{slug}', [AuthorController::class, 'show'])->name('show');
});

// Downloads
Route::prefix('download')->name('download.')->group(function () {
    Route::get('/{id}', [DownloadController::class, 'download'])->name('file');
    Route::get('/book/{bookId}/{format}', [DownloadController::class, 'downloadByFormat'])->name('format');
});

// Contact
Route::prefix('contato')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'store'])->name('store');
});

// About
Route::get('/sobre', [AboutController::class, 'index'])->name('about.index');

// Admin
Route::prefix('admin/import')->name('import.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::post('/search/openlibrary', [ImportController::class, 'searchOpenLibrary'])->name('search.openlibrary');
    Route::post('/search/gutenberg', [ImportController::class, 'searchGutenberg'])->name('search.gutenberg');
    Route::post('/import', [ImportController::class, 'import'])->name('do');
});

