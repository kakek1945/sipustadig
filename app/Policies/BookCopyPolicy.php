<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BookCopy;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookCopyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BookCopy $bookCopy): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_copies');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BookCopy $bookCopy): bool
    {
        return $user->hasPermission('manage_copies');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookCopy $bookCopy): bool
    {
        return $user->hasPermission('manage_copies') && $user->isStaff();
    }
}
