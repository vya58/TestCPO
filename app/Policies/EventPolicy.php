<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    /**
     * Determine whether the user can delete the model.
     *
     * @param  User $user
     * @param  Event $comment
     * @return Response|bool
     */
    public function delete(User $user, Event $event): bool
    {
        return ($user->id === $event->user_id);
    }
}
