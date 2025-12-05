<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'books' => Book::count(),
            'authors' => Author::count(),
            'downloads' => Book::sum('total_downloads'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
