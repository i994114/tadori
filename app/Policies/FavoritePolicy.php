<?php

namespace App\Policies;

use App\User;
use App\Favorite;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavoritePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any favorites.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function view(User $user, Favorite $favorite)
    {
        //
    }

    /**
     * Determine whether the user can create favorites.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, array $data)
    {

        return $user->id === (int)$data['user_id'];
    }

    /**
     * Determine whether the user can update the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function update(User $user, Favorite $favorite)
    {
        //
    }

    /**
     * Determine whether the user can delete the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function delete(User $user, Favorite $favorite)
    {
        //
    }

    /**
     * Determine whether the user can restore the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function restore(User $user, Favorite $favorite)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the favorite.
     *
     * @param  \App\User  $user
     * @param  \App\Favorite  $favorite
     * @return mixed
     */
    public function forceDelete(User $user, Favorite $favorite)
    {
        return $user->id === $favorite->user_id;
    }
}
