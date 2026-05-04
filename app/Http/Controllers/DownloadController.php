<?php

// ============================================
// app/Http/Controllers/DownloadController.php
// ============================================

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download($id)
    {
        $file = File::with('book')->findOrFail($id);

        // Record the download
        $file->recordDownload();

        \App\Models\AnalyticsEvent::create([
            'event_type' => 'file_download',
            'book_id'    => $file->book_id,
            'file_id'    => $file->id,
            'ip_address' => request()->ip(),
        ]);

        // If file is in local storage
        if (Storage::disk('public')->exists($file->file_url)) {
            return Storage::disk('public')->download($file->file_url);
        }

        // If external URL, redirect
        return redirect($file->file_url);
    }

    // Download by book and format
    public function downloadByFormat($bookId, $format)
    {
        $file = File::where('book_id', $bookId)
            ->where('format', strtoupper($format))
            ->where('is_active', true)
            ->firstOrFail();

        return $this->download($file->id);
    }
}