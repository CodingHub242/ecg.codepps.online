<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\Theft;
use App\Models\User;

class TheftPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->can('view_any_theft');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Theft $theft): bool
    {
        return $user->can('view_theft');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_theft');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Theft $theft): bool
    {
        return $user->can('update_theft');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Theft $theft): bool
    {
        return $user->can('delete_theft');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete-any_theft');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Theft $theft): bool
    {
        return $user->can('restore_theft');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore-any_theft');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Theft $theft): bool
    {
        return $user->can('replicate_theft');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_theft');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Theft $theft): bool
    {
        return $user->can('force-delete_theft');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force-delete-any_theft');
    }
}
