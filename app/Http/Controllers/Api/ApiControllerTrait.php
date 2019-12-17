<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Api
 */
trait ApiControllerTrait
{
    /**
     * @param Request $request
     * @return User
     * @throws UserException
     */
    protected function checkUserOfTokenExists(Request $request): User
    {
        $token = $request->bearerToken();
        $user = (new UserRepository())->getUserByJwtToken($token);
        if ($user === null) {
            throw new UserException('User with this token does not exist');
        }
        return $user;
    }
}