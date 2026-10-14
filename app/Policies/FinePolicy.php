<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Fine;
use App\Enums\FineStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_fines');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fine $fine): bool
    {
        return $user->hasPermission('manage_fines');
    }

    /**
     * Determine whether the user can pay the fine.
     */
    public function pay(User $user, Fine $fine): bool
    {
        return $user->hasPermission('manage_fines') && $fine->status === FineStatus::Pending;
    }

    /**
     * Determine whether the user can waive the fine.
     */
    public function waive(User $user, Fine $fine): bool
    {
        return $user->hasPermission('manage_fines') && $user->isStaff() && $fine->status === FineStatus::Pending;
    }
}
