<?php

namespace App\Policies;

use App\Step;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class StepPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any steps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the step.
     *
     * @param  \App\User  $user
     * @param  \App\Step  $step
     * @return mixed
     */
    public function view(User $user, Step $step)
    {
        //
    }

    /**
     * Determine whether the user can create steps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user !== null;
    }

    //子STEP作成ポリシー
    public function createSubStep(User $user, Step $step)
    {
        return $user->id === $step->owner_id;
    }

    /**
     * Determine whether the user can update the step.
     *
     * @param  \App\User  $user
     * @param  \App\Step  $step
     * @return mixed
     */
    public function update(User $user, Step $step)
    {
        return $user->id === $step->owner_id;
    }

    /**
     * Determine whether the user can delete the step.
     *
     * @param  \App\User  $user
     * @param  \App\Step  $step
     * @return mixed
     */
    public function delete(User $user, Step $step)
    {
        return $user->id === $step->owner_id;
    }

    /**
     * Determine whether the user can restore the step.
     *
     * @param  \App\User  $user
     * @param  \App\Step  $step
     * @return mixed
     */
    public function restore(User $user, Step $step)
    {
        return $user->id === $step->owner_id;
    }

    /**
     * Determine whether the user can permanently delete the step.
     *
     * @param  \App\User  $user
     * @param  \App\Step  $step
     * @return mixed
     */
    public function forceDelete(User $user, Step $step)
    {
        return $user->id === $step->owner_id;
    }
}
