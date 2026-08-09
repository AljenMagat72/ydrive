<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\BaseUser;
use App\Models\Driver;
use Illuminate\Auth\Access\Response;

class DriverPolicy
{
    public function read(?BaseUser $user, Driver $driver)
    {
        if (request()->attributes->get('is_admin')) {
            return Response::allow();
        }

        if ($user->type() === UserType::ADMIN || $driver->id === $user->id) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }
}
