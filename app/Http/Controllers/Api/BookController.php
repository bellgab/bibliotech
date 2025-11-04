<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * Display a listing of books.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Book::with(['author', 'category']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('author', function ($author) use ($search) {
                      $author->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Filter by author
        if ($request->has('author_id')) {
            $query->where('author_id', $request->get('author_id'));
        }

        // Filter by availability
        if ($request->has('available') && $request->get('available') === 'true') {
            $query->where('available_copies', '>', 0);
        }

        $books = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $books,
        ]);
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books|max:20',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publication_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'description' => 'nullable|string',
            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'total_copies' => 'required|integer|min:1',
        ]);

        $book = Book::create([
            ...$request->all(),
            'available_copies' => $request->total_copies,
        ]);

        $book->load(['author', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Book created successfully',
            'data' => $book,
        ], 201);
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book): JsonResponse
    {
        $book->load(['author', 'category', 'borrowers']);

        return response()->json([
            'success' => true,
            'data' => $book,
        ]);
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|max:20|unique:books,isbn,' . $book->id,
            'author_id' => 'sometimes|required|exists:authors,id',
            'category_id' => 'sometimes|required|exists:categories,id',
            'publication_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'description' => 'nullable|string',
            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'total_copies' => 'sometimes|required|integer|min:1',
            'available_copies' => 'sometimes|required|integer|min:0',
        ]);

        $book->update($request->all());
        $book->load(['author', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Book updated successfully',
            'data' => $book,
        ]);
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book): JsonResponse
    {
        if ($book->borrowers()->wherePivot('returned_at', null)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete book that is currently borrowed',
            ], 422);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book deleted successfully',
        ]);
    }
}
