<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
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

// Categories
Route::get('/categorias', [App\Http\Controllers\CategoryController::class, 'index'])->name('categorias.index');

// Downloads
Route::prefix('download')->name('download.')->group(function () {
    Route::get('/{id}', [DownloadController::class, 'download'])->name('file');
    Route::get('/book/{bookId}/{format}', [DownloadController::class, 'downloadByFormat'])->name('format');
});

// Ratings
Route::prefix('ratings')->name('ratings.')->group(function () {
    Route::post('/book/{bookId}', [App\Http\Controllers\RatingController::class, 'store'])->name('store');
    Route::get('/book/{bookId}/can-rate', [App\Http\Controllers\RatingController::class, 'canRate'])->name('can-rate');
    Route::get('/book/{bookId}', [App\Http\Controllers\RatingController::class, 'index'])->name('index');
});

// Contact
Route::prefix('contato')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'store'])->name('store');
});

// About
Route::get('/sobre', [AboutController::class, 'index'])->name('about.index');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Robots.txt (dynamic)
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Admin Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.do');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Admin Protected Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Resources
    Route::resource('authors', App\Http\Controllers\Admin\AuthorController::class);
    Route::resource('books', App\Http\Controllers\Admin\BookController::class);
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('quotes', App\Http\Controllers\Admin\QuoteController::class);

    // Import (Moved from public scope)
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::post('/search/openlibrary', [ImportController::class, 'searchOpenLibrary'])->name('search.openlibrary');
        Route::post('/search/gutenberg', [ImportController::class, 'searchGutenberg'])->name('search.gutenberg');
        Route::post('/import', [ImportController::class, 'import'])->name('do');
    });

    // JSON Import
    Route::prefix('import-json')->name('import-json.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\JsonImportController::class, 'index'])->name('index');
        Route::post('/preview', [App\Http\Controllers\Admin\JsonImportController::class, 'preview'])->name('preview');
        Route::post('/import', [App\Http\Controllers\Admin\JsonImportController::class, 'import'])->name('import');
        Route::post('/create-author', [App\Http\Controllers\Admin\JsonImportController::class, 'createAuthor'])->name('create-author');
        Route::post('/create-category', [App\Http\Controllers\Admin\JsonImportController::class, 'createCategory'])->name('create-category');
    });
});



