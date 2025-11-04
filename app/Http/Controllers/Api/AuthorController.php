<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    /**
     * Display a listing of authors.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Author::withCount('books');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nationality', 'like', "%{$search}%");
        }

        // Filter by nationality
        if ($request->has('nationality')) {
            $query->where('nationality', $request->get('nationality'));
        }

        $authors = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $authors,
        ]);
    }

    /**
     * Store a newly created author.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'biography' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'death_date' => 'nullable|date|after:birth_date',
            'nationality' => 'nullable|string|max:100',
        ]);

        $author = Author::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Author created successfully',
            'data' => $author,
        ], 201);
    }

    /**
     * Display the specified author.
     */
    public function show(Author $author): JsonResponse
    {
        $author->load(['books.category']);

        return response()->json([
            'success' => true,
            'data' => $author,
        ]);
    }

    /**
     * Update the specified author.
     */
    public function update(Request $request, Author $author): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'biography' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'death_date' => 'nullable|date|after:birth_date',
            'nationality' => 'nullable|string|max:100',
        ]);

        $author->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Author updated successfully',
            'data' => $author,
        ]);
    }

    /**
     * Remove the specified author.
     */
    public function destroy(Author $author): JsonResponse
    {
        if ($author->books()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete author who has books in the system',
            ], 422);
        }

        $author->delete();

        return response()->json([
            'success' => true,
            'message' => 'Author deleted successfully',
        ]);
    }
}
