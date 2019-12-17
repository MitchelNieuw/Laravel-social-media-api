<?php

namespace App\Http\Middleware;

use App\Enums\ResponseMessageEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

/**
 * @package App\Http\Middleware
 */
class CheckJWTToken extends BaseMiddleware
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $exception) {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            $user = JWTAuth::setToken($token)->toUser();
            $user->update(['jwt_token' => $token]);
            return $next($request);
        } catch (TokenBlacklistedException $exception) {
            Log::critical($exception->getMessage());
            return response()->json(['message' => 'Token blacklisted']);
        } catch (TokenInvalidException $exception) {
            Log::critical($exception->getMessage());
            return response()->json(['message' => 'Token invalid']);
        } catch (JWTException $exception) {
            Log::critical($exception->getMessage());
            return response()->json(['message' => ResponseMessageEnum::OOPS_SOMETHING_WENT_WRONG]);
        }
        return $next($request);
    }
}