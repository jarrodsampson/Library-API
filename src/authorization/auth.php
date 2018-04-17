<?php

namespace App\Authorization;

use App\Database\DataAccess;

class Auth
{
    public function getUserByToken($token)
    {
        if ($token != 'secret')
        {
            throw new UnauthorizedException('Invalid Token');
        }
        return;
    }
}

