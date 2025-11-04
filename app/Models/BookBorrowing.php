<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookBorrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'fine_amount',
        'notes'
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2'
    ];

    /**
     * Get the user that borrowed the book.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that was borrowed.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if the book is overdue.
     */
    public function isOverdue()
    {
        return $this->returned_at === null && $this->due_date < now();
    }

    /**
     * Accessor for "is_overdue" to be used in views/mails.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->isOverdue();
    }

    

    /**
     * Calculate fine amount for overdue books.
     */
    public function calculateFine($dailyFineRate = 5.0)
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $daysOverdue = now()->diffInDays($this->due_date);
        return $daysOverdue * $dailyFineRate;
    }

    /**
     * Get the number of days overdue.
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    /**
     * Get the status of the borrowing.
     */
    public function getStatusAttribute(): string
    {
        if ($this->returned_at) {
            return 'returned';
        }

        if ($this->isOverdue()) {
            return 'overdue';
        }

        return 'active';
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'returned':
                return 'success';
            case 'overdue':
                return 'danger';
            case 'active':
                return 'primary';
            default:
                return 'secondary';
        }
    }
}
