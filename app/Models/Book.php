<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'publication_year',
        'description',
        'pages',
        'language',
        'publisher',
        'author_id',
        'category_id',
        'available_copies',
        'total_copies',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages' => 'integer',
        'available_copies' => 'integer',
        'total_copies' => 'integer',
    ];

    /**
     * Get the author that owns the book.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get the category that owns the book.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The users that have borrowed this book.
     */
    public function borrowers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_borrowings')
                    ->withPivot(['borrowed_at', 'due_date', 'returned_at'])
                    ->withTimestamps();
    }

    /**
     * Get all borrowing records for this book.
     */
    public function borrows()
    {
        return $this->hasMany(BookBorrowing::class);
    }

    /**
     * Check if the book is available for borrowing.
     */
    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }

    /**
     * Get the number of borrowed copies.
     */
    public function getBorrowedCopiesAttribute(): int
    {
        return $this->total_copies - $this->available_copies;
    }
}
