<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\RecordNotFoundException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

abstract class BaseUser extends User
{
    abstract protected function type(): UserType;

    public function resolveRouteBinding($value, $field = null)
    {
        $user = Auth::user();
        $id = $value;

        if ($value === 'me') {
            if ( $user === null || $this->type() !== $user->type()) {
                throw new RecordNotFoundException();
            }
            $id = $user->id;
        }

        return $this->where('id', $id)->firstOrFail();
    }
}
