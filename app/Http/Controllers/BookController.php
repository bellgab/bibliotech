<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with(['author', 'category']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('author', function($authorQuery) use ($search) {
                      $authorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->has('category') && $request->get('category') != '') {
            $query->where('category_id', $request->get('category'));
        }

        // Filter by availability
        if ($request->has('available') && $request->get('available') == '1') {
            $query->where('available_copies', '>', 0);
        }

        $books = $query->paginate(12);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();
        return view('books.create', compact('authors', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'publication_year' => 'required|integer|min:1000|max:' . date('Y'),
            'description' => 'nullable|string',
            'pages' => 'required|integer|min:1',
            'language' => 'required|string|max:50',
            'publisher' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'total_copies' => 'required|integer|min:1',
        ]);

        $validated['available_copies'] = $validated['total_copies'];

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Könyv sikeresen hozzáadva!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['author', 'category', 'borrows.user']);
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $authors = Author::all();
        $categories = Category::all();
        return view('books.edit', compact('book', 'authors', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'publication_year' => 'required|integer|min:1000|max:' . date('Y'),
            'description' => 'nullable|string',
            'pages' => 'required|integer|min:1',
            'language' => 'required|string|max:50',
            'publisher' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'total_copies' => 'required|integer|min:1',
        ]);

        // Ensure available copies don't exceed total copies
        if (isset($validated['total_copies'])) {
            $borrowedCopies = $book->total_copies - $book->available_copies;
            $validated['available_copies'] = max(0, $validated['total_copies'] - $borrowedCopies);
        }

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Könyv sikeresen frissítve!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        // Check if book has active borrows
        if ($book->borrows()->whereNull('returned_at')->exists()) {
            return redirect()->route('books.index')->with('error', 'A könyv nem törölhető, mert aktív kölcsönzés van rajta!');
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Könyv sikeresen törölve!');
    }
}
