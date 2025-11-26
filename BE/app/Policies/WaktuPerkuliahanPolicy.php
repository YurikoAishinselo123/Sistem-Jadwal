<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WaktuPerkuliahan;
use Illuminate\Auth\Access\Response;

class WaktuPerkuliahanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WaktuPerkuliahan $waktuPerkuliahan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WaktuPerkuliahan $waktuPerkuliahan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WaktuPerkuliahan $waktuPerkuliahan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WaktuPerkuliahan $waktuPerkuliahan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WaktuPerkuliahan $waktuPerkuliahan): bool
    {
        return false;
    }
}
