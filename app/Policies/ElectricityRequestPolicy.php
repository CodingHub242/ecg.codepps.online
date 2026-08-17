<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\ElectricityRequest;
use App\Models\User;

class ElectricityRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->can('view_any_electricity::request');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ElectricityRequest $electricityRequest): bool
    {
        return $user->can('view_electricity::request');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_electricity::request');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ElectricityRequest $electricityRequest): bool
    {
        return $user->can('update_electricity::request');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ElectricityRequest $electricityRequest): bool
    {
        return $user->can('delete_electricity::request');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete-any_electricity::request');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ElectricityRequest $electricityRequest): bool
    {
        return $user->can('restore_electricity::request');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore-any_electricity::request');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ElectrcityRequest $electricityRequest): bool
    {
        return $user->can('replicate_electricity::request');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_electricity::request');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ElectricityRequest $electricityRequest): bool
    {
        return $user->can('force-delete_electricity::request');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force-delete-any_electricity::request');
    }
}
