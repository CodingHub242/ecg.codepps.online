<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\Fault;
use App\Models\User;

class FaultPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->can('view_any_fault');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fault $fault): bool
    {
        return $user->can('view_fault');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_fault');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fault $fault): bool
    {
        return $user->can('update_fault');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fault $fault): bool
    {
        return $user->can('delete_fault');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete-any_fault');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fault $fault): bool
    {
        return $user->can('restore_fault');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore-any_fault');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Fault $fault): bool
    {
        return $user->can('replicate_fault');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_fault');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fault $fault): bool
    {
        return $user->can('force-delete_fault');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force-delete-any_fault');
    }
}
