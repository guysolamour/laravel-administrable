<?php

namespace Guysolamour\Administrable\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class GuardPolicy
{
    use HandlesAuthorization;


    public function create($auth)
    {
        if (
            $auth->hasRole(config('administrable.guard')) ||
            $auth->hasRole('super-' . config('administrable.guard'))
        )
        {
            return true;
        }

        return false;
    }


    public function update($auth, $guard)
    {
        if (
            $auth->hasRole(config('administrable.guard')) ||
            $auth->hasRole('super-' . config('administrable.guard'))
        ) {
            return true;
        }

        return $auth->getKey() == $guard->getKey();
    }

    public function updatePassword($auth, $guard)
    {
        if (
            $auth->hasRole(config('administrable.guard')) ||
            $auth->hasRole('super-' . config('administrable.guard'))
        ) {
            return true;
        }


        return $auth->getKey() == $guard->getKey();
    }

    public function changeAvatar($auth, $guard)
    {
        if (
            $auth->hasRole(config('administrable.guard')) ||
            $auth->hasRole('super-' . config('administrable.guard'))
        ) {
            return true;
        }


        return $auth->getKey() == $guard->getKey();
    }

    public function delete($auth, $guard)
    {
        if (
            $auth->getKey() != $guard->getKey() &&
            ($auth->hasRole('super-' . config('administrable.guard')) ||
            $auth->hasRole('super-' . config('administrable.guard')))
        ){
           return true;
        }

        return false;
    }


}
