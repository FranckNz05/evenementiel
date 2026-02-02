<?php

namespace App\Policies;

use App\Models\OrganizerAccessCode;
use App\Models\User;

class OrganizerAccessCodePolicy
{
    public function delete(User $user, OrganizerAccessCode $accessCode)
    {
        return $user->organizer->id === $accessCode->organizer_id;
    }
}