<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseMessageEnum;
use App\Exceptions\UserException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

trait ApiControllerTrait
{
    /**
     * @throws UserException
     */
    protected function checkUserOfTokenExists(Request $request): User
    {
        $user = (new UserRepository())->getUserByJwtToken($request->bearerToken());
        if ($user === null) {
            throw new UserException(ResponseMessageEnum::USER_WITH_TOKEN_DOESNT_EXIST);
        }
        return $user;
    }
}
