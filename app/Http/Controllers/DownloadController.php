<?php

// ============================================
// app/Http/Controllers/DownloadController.php
// ============================================

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        // Arquivo no bucket R2: stream direto com headers de download
        if ($file->is_stored_in_bucket) {
            $path      = $file->storage_path;
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $filename  = Str::slug($file->book->title) . '.' . $extension;
            $mimeTypes = [
                'pdf'  => 'application/pdf',
                'epub' => 'application/epub+zip',
                'mobi' => 'application/x-mobipocket-ebook',
                'txt'  => 'text/plain',
            ];
            $mime = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

            $stream = Storage::disk('r2')->readStream($path);

            return response()->stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'no-cache',
            ]);
        }

        // URL externa: redirecionar normalmente
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