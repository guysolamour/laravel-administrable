<?php

namespace Guysolamour\Administrable\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function before($user = null, $ability)
    {
        if (get_guard()) {
            return true;
        }
    }

    public function create($user)
    {
       return true;
    }

    public function reply($user, $comment)
    {
        return true;
    }

    public function update($user, $comment)
    {
        return $user->getKey() == $comment->commenter_id;
    }

    public function delete($user, $comment)
    {
        return $user->getKey() == $comment->commenter_id;
    }
}
