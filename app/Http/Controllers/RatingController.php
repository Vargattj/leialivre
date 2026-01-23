<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Store a new rating for a book
     */
    public function store(Request $request, $bookId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $book = Book::findOrFail($bookId);
        $ipAddress = $request->ip();

        // Check if user already rated this book
        $existingRating = Rating::where('book_id', $bookId)
            ->where('ip_address', $ipAddress)
            ->first();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'Você já avaliou este livro.',
            ], 422);
        }

        // Create the rating
        $rating = Rating::create([
            'book_id' => $bookId,
            'ip_address' => $ipAddress,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update book's average rating and total ratings
        $this->updateBookRatingStats($book);

        return response()->json([
            'success' => true,
            'message' => 'Avaliação enviada com sucesso!',
            'data' => [
                'average_rating' => round($book->fresh()->average_rating, 1),
                'total_ratings' => $book->fresh()->total_ratings,
            ],
        ]);
    }

    /**
     * Check if user can rate a book
     */
    public function canRate(Request $request, $bookId)
    {
        $ipAddress = $request->ip();
        
        $hasRated = Rating::where('book_id', $bookId)
            ->where('ip_address', $ipAddress)
            ->exists();

        return response()->json([
            'can_rate' => !$hasRated,
        ]);
    }

    /**
     * Get ratings for a book
     */
    public function index($bookId)
    {
        $ratings = Rating::where('book_id', $bookId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($ratings);
    }

    /**
     * Update book rating statistics
     */
    private function updateBookRatingStats(Book $book)
    {
        $stats = Rating::where('book_id', $book->id)
            ->select(
                DB::raw('AVG(rating) as average'),
                DB::raw('COUNT(*) as total')
            )
            ->first();

        $book->update([
            'average_rating' => $stats->average ?? 0,
            'total_ratings' => $stats->total ?? 0,
        ]);
    }
}
