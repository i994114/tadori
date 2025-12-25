<?php

namespace App\Policies;

use App\User;
use App\Challenge;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChallengePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any challenges.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the challenge.
     *
     * @param  \App\User  $user
     * @param  \App\Challenge  $challenge
     * @return mixed
     */
    public function view(User $user, Challenge $challenge)
    {
        //
    }

    /**
     * Determine whether the user can create challenges.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can update the challenge.
     *
     * @param  \App\User  $user
     * @param  \App\Challenge  $challenge
     * @return mixed
     */
    public function update(User $user, Challenge $challenge)
    {
        //
    }

    /**
     * Determine whether the user can delete the challenge.
     *
     * @param  \App\User  $user
     * @param  \App\Challenge  $challenge
     * @return mixed
     */
    public function delete(User $user, Challenge $challenge)
    {
        return $user->id === $challenge->user_id;
    }

    /**
     * Determine whether the user can restore the challenge.
     *
     * @param  \App\User  $user
     * @param  \App\Challenge  $challenge
     * @return mixed
     */
    public function restore(User $user, Challenge $challenge)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the challenge.
     *
     * @param  \App\User  $user
     * @param  \App\Challenge  $challenge
     * @return mixed
     */
    public function forceDelete(User $user, Challenge $challenge)
    {
        //
    }
}
