<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'membership_number',
        'membership_type',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * The book borrowings that belong to the user.
     */
    public function borrows()
    {
        return $this->hasMany(BookBorrowing::class);
    }

    /**
     * The books that the user has borrowed.
     */
    public function borrowedBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_borrowings')
                    ->withPivot(['borrowed_at', 'due_date', 'returned_at'])
                    ->withTimestamps();
    }

    /**
     * Get currently borrowed books (not yet returned).
     */
    public function currentlyBorrowedBooks(): BelongsToMany
    {
        return $this->borrowedBooks()->whereNull('book_borrowings.returned_at');
    }

    /**
     * Get books that are overdue.
     */
    public function overdueBooks(): BelongsToMany
    {
        return $this->currentlyBorrowedBooks()
                    ->where('book_borrowings.due_date', '<', now());
    }

    /**
     * Check if user can borrow more books.
     */
    public function canBorrowBooks(): bool
    {
        return $this->is_active && $this->currentlyBorrowedBooks()->count() < 5;
    }

    /**
     * Get the number of currently borrowed books.
     */
    public function getBorrowedBooksCountAttribute(): int
    {
        return $this->currentlyBorrowedBooks()->count();
    }

    /**
     * Get the user's role (alias for membership_type).
     */
    public function getRoleAttribute(): string
    {
        return $this->membership_type;
    }

    /**
     * Set the user's role (alias for membership_type).
     */
    public function setRoleAttribute($value): void
    {
        $this->membership_type = $value;
    }

    /**
     * Check if user is admin.
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->membership_type === 'admin';
    }

    /**
     * Check if user is librarian.
     */
    public function getIsLibrarianAttribute(): bool
    {
        return $this->membership_type === 'librarian';
    }

    /**
     * Check if user has admin or librarian role.
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, ['admin', 'librarian']) && 
               ($this->membership_type === $role || $this->membership_type === 'admin');
    }
}
