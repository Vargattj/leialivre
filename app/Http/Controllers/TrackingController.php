<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track(Request $request)
    {
        $validated = $request->validate([
            'event_type' => 'required|string',
            'book_id'    => 'nullable|exists:books,id',
            'file_id'    => 'nullable|exists:files,id',
        ]);

        AnalyticsEvent::create([
            'event_type' => $validated['event_type'],
            'book_id'    => $validated['book_id'] ?? null,
            'file_id'    => $validated['file_id'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        if ($validated['event_type'] === 'purchase_click' && !empty($validated['book_id'])) {
            \App\Models\Book::where('id', $validated['book_id'])->increment('purchase_clicks');
        }

        return response()->json(['success' => true]);
    }
}
