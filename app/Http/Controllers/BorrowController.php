<?php

namespace App\Http\Controllers;

use App\Models\BookBorrowing;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BookBorrowing::with(['book.author', 'user']);

        // Filter by return status
        if ($request->has('status')) {
            switch ($request->get('status')) {
                case 'active':
                    $query->whereNull('returned_at');
                    break;
                case 'returned':
                    $query->whereNotNull('returned_at');
                    break;
                case 'overdue':
                    $query->whereNull('returned_at')
                          ->where('due_date', '<', Carbon::now());
                    break;
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('book', function($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%")
                             ->orWhere('isbn', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('membership_number', 'like', "%{$search}%");
                });
            });
        }

        $borrows = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('borrows.index', compact('borrows'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $books = Book::where('available_copies', '>', 0)->with('author')->get();
        $users = User::where('is_active', true)->get();
        
        return view('borrows.create', compact('books', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $user = User::findOrFail($validated['user_id']);

        // Check if book is available
        if ($book->available_copies <= 0) {
            return redirect()->back()->with('error', 'A könyv nem elérhető kölcsönzésre!');
        }

        // Check if user can borrow more books
        if (!$user->canBorrowBooks()) {
            return redirect()->back()->with('error', 'A felhasználó nem kölcsönözhet több könyvet!');
        }

        // Create borrow record
        $validated['borrowed_at'] = Carbon::now();
        BookBorrowing::create($validated);

        // Decrease available copies
        $book->decrement('available_copies');

        return redirect()->route('borrows.index')->with('success', 'Kölcsönzés sikeresen rögzítve!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BookBorrowing $borrow)
    {
        $borrow->load(['book.author', 'user']);
        return view('borrows.show', compact('borrow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookBorrowing $borrow)
    {
        if ($borrow->returned_at) {
            return redirect()->route('borrows.index')->with('error', 'Már visszaadott könyv nem szerkeszthető!');
        }

        return view('borrows.edit', compact('borrow'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookBorrowing $borrow)
    {
        if ($borrow->returned_at) {
            return redirect()->route('borrows.index')->with('error', 'Már visszaadott könyv nem szerkeszthető!');
        }

        $validated = $request->validate([
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $borrow->update($validated);

        return redirect()->route('borrows.index')->with('success', 'Kölcsönzés sikeresen frissítve!');
    }

    /**
     * Return a borrowed book.
     */
    public function returnBook(BookBorrowing $borrow)
    {
        if ($borrow->returned_at) {
            return redirect()->route('borrows.index')->with('error', 'A könyv már vissza van adva!');
        }

        // Calculate fine if overdue
        $fine = 0;
        if ($borrow->isOverdue()) {
            $fine = $borrow->calculateFine();
        }

        // Update borrow record
        $borrow->update([
            'returned_at' => Carbon::now(),
            'fine_amount' => $fine,
        ]);

        // Increase available copies
        $borrow->book->increment('available_copies');

        $message = 'Könyv sikeresen visszaadva!';
        if ($fine > 0) {
            $message .= " Késedelmi díj: {$fine} Ft";
        }

        return redirect()->route('borrows.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookBorrowing $borrow)
    {
        if ($borrow->returned_at === null) {
            // If book is not returned, increment available copies
            $borrow->book->increment('available_copies');
        }

        $borrow->delete();

        return redirect()->route('borrows.index')->with('success', 'Kölcsönzés törölve!');
    }
}
