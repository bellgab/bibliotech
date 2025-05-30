<?php

namespace App\Policies;

use App\Models\BookReview;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view reviews
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BookReview $bookReview): bool
    {
        return true; // Everyone can view individual reviews
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active; // Only active users can create reviews
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BookReview $bookReview): bool
    {
        // Users can edit their own reviews if not yet approved, or admins/librarians can edit any
        return ($user->id === $bookReview->user_id && !$bookReview->is_approved) ||
               $user->is_admin || 
               $user->is_librarian;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookReview $bookReview): bool
    {
        // Users can delete their own reviews, or admins/librarians can delete any
        return $user->id === $bookReview->user_id || 
               $user->is_admin || 
               $user->is_librarian;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, BookReview $bookReview): bool
    {
        return $user->is_admin || $user->is_librarian;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BookReview $bookReview): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BookReview $bookReview): bool
    {
        return $user->is_admin;
    }
}
