<?php

namespace App\Services;

use App\Exceptions\PasswordException;
use App\Exceptions\UserException;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @package App\Services
 */
class AuthenticationService
{
    /**
     * @param Request $request
     * @return User
     * @throws PasswordException
     * @throws UserException
     * @throws ValidationException
     */
    public function apiLogin(Request $request): User
    {
        $this->validateLoginRequest($request);
        $user = $this->checkUserWithThisEmailExists($request->get('email'));
        $this->checkCorrectPassword($request->get('password'), $user->getAttribute('password'));
        $user->setAttribute('jwt_token', JWTAuth::fromUser($user));
        $user->save();
        return $user;
    }

    /**
     * @param Request $request
     * @return User
     */
    public function apiRegister(Request $request): User
    {
        $this->validateRegisterRequest($request);
        return $this->createUser($request, $this->storeProfilePicture($request));
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    private function validateLoginRequest(Request $request): void
    {

        $validator = validator()->make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * @param string $email
     * @return User
     * @throws UserException
     */
    private function checkUserWithThisEmailExists(string $email): User
    {
        $user = (new UserRepository())->getUserByEmail($email);
        if ($user === null) {
            throw new UserException('User with this email not found');
        }
        return $user;
    }

    /**
     * @param string $requestPassword
     * @param string $userPassword
     * @throws PasswordException
     */
    private function checkCorrectPassword(string $requestPassword, string $userPassword): void
    {
        if (!Hash::check($requestPassword, $userPassword)) {
            throw new PasswordException('Password not correct');
        }
    }

    /**
     * @param Request $request
     */
    private function validateRegisterRequest(Request $request): void
    {
        $validator = validator()->make($request->all(), [
            'name' => 'required|string',
            'tag' => 'required|string|unique:users,tag',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            throw new BadRequestHttpException($validator->getMessageBag()->first());
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    private function storeProfilePicture(Request $request): string
    {
        $fileOriginalName = 'profile.png';
        if ($request->hasFile('profilePicture')) {
            $file = $request->file('profilePicture');
            $fileOriginalName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('profilePictures').'/', $fileOriginalName);
        }
        return $fileOriginalName;
    }

    /**
     * @param Request $request
     * @param string $profilePicture
     * @return User
     */
    private function createUser(Request $request, string $profilePicture): User
    {
        $user = User::create([
            'name' => $request->get('name'),
            'tag' => $request->get('tag'),
            'email' => $request->get('email'),
            'profilePicture' => $profilePicture,
            'password' => Hash::make($request->get('password')),
        ]);
        $user->update([
            'jwt_token' => JWTAuth::fromUser($user),
        ]);
        return $user;
    }
}