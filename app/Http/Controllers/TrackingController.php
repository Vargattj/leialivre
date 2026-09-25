<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track(Request $request)
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:60',
            'book_id'    => 'nullable|exists:books,id',
            'file_id'    => 'nullable|exists:files,id',
            'metadata'   => 'nullable|array',
        ]);

        AnalyticsEvent::create([
            'event_type' => $validated['event_type'],
            'book_id'    => $validated['book_id'] ?? null,
            'file_id'    => $validated['file_id'] ?? null,
            'ip_address' => $request->ip(),
            'metadata'   => $validated['metadata'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }
}
