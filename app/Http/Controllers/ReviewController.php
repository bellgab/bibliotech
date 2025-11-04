<?php

namespace App\Http\Controllers;

use App\Models\BookReview;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', BookReview::class);
        
        $query = BookReview::with(['book.author', 'user']);

        // Filter by book if book_id is provided
        if ($request->has('book_id')) {
            $query->where('book_id', $request->get('book_id'));
        }

        // Filter by approval status
        if ($request->has('status')) {
            switch ($request->get('status')) {
                case 'approved':
                    $query->approved();
                    break;
                case 'pending':
                    $query->pending();
                    break;
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('book', function($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', BookReview::class);
        
        $book = null;
        if ($request->has('book_id')) {
            $book = Book::findOrFail($request->get('book_id'));
            
            // Check if user has already reviewed this book
            $existingReview = BookReview::where('user_id', Auth::id())
                                      ->where('book_id', $book->id)
                                      ->first();
            
            if ($existingReview) {
                return redirect()->route('books.show', $book)
                               ->with('error', 'Ezt a könyvet már értékelted!');
            }
        }

        $books = Book::with('author')->get();
        return view('reviews.create', compact('books', 'book'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', BookReview::class);
        
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // Check if user has already reviewed this book
        $existingReview = BookReview::where('user_id', Auth::id())
                                  ->where('book_id', $book->id)
                                  ->first();

        if ($existingReview) {
            return redirect()->route('books.show', $book)
                           ->with('error', 'Ezt a könyvet már értékelted!');
        }

        // Check if user has borrowed this book (optional business rule)
        $hasBorrowed = $book->borrows()
                           ->where('user_id', Auth::id())
                           ->whereNotNull('returned_at')
                           ->exists();

        if (!$hasBorrowed) {
            return redirect()->route('books.show', $book)
                           ->with('error', 'Csak olyan könyveket értékelhetsz, amelyeket már kölcsönöztél és visszahoztál!');
        }

        $validated['user_id'] = Auth::id();

        BookReview::create($validated);

        return redirect()->route('books.show', $book)
                       ->with('success', 'Értékelés sikeresen elküldve! Moderáció után jelenik meg.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BookReview $review)
    {
        $this->authorize('view', $review);
        
        $review->load(['book.author', 'user', 'approvedBy']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookReview $review)
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookReview $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return redirect()->route('books.show', $review->book)
                       ->with('success', 'Értékelés sikeresen frissítve!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookReview $review)
    {
        $this->authorize('delete', $review);

        $book = $review->book;
        $review->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Értékelés sikeresen törölve!'
            ]);
        }

        return redirect()->route('books.show', $book)
                       ->with('success', 'Értékelés törölve!');
    }

    /**
     * Approve a review.
     */
    public function approve(BookReview $review)
    {
        $this->authorize('approve', $review);

        $review->approve(Auth::user());

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Értékelés sikeresen jóváhagyva!'
            ]);
        }

        return redirect()->route('reviews.index')
                       ->with('success', 'Értékelés jóváhagyva!');
    }

    /**
     * Get reviews for a specific book.
     */
    public function bookReviews(Book $book)
    {
        $reviews = $book->approvedReviews()
                       ->with('user')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('reviews.book-reviews', compact('book', 'reviews'));
    }
}
