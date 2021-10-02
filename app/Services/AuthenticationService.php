<?php

namespace App\Services;

use App\Exceptions\{PasswordException, UserException};
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthenticationService
{
    /**
     * @throws BindingResolutionException
     * @throws PasswordException
     * @throws UserException
     * @throws ValidationException
     */
    public function apiLogin(Request $request): User
    {
        $this->validateLoginRequest($request);
        $user = $this->checkUserWithThisEmailExists($request->get('email'));
        $this->checkCorrectPassword($request->get('password'), $user->password);
        return $user;
    }

    /**
     * @throws BindingResolutionException
     */
    public function apiRegister(Request $request): User
    {
        $this->validateRegisterRequest($request);
        return $this->createUser($request, $this->storeProfilePicture($request));
    }

    /**
     * @param Request $request
     * @throws ValidationException
     * @throws BindingResolutionException
     */
    private function validateLoginRequest(Request $request): void
    {

        $validator = validator()->make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * @throws UserException
     */
    private function checkUserWithThisEmailExists(string $email): User
    {
        if (($user = (new UserRepository)->getUserByEmail($email)) === null) {
            throw new UserException('User with this email not found!');
        }
        return $user;
    }

    /**
     * @throws PasswordException
     */
    private function checkCorrectPassword(string $requestPassword, string $userPassword): void
    {
        if (!Hash::check($requestPassword, $userPassword)) {
            throw new PasswordException('Wrong email password combination!');
        }
    }

    /**
     * @throws BindingResolutionException
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

    private function storeProfilePicture(Request $request): string
    {
        $fileOriginalName = 'profile.png';
        if ($request->hasFile('profilePicture')) {
            $file = $request->file('profilePicture');
            $time = time();
            $fileOriginalName = "{$time}_{$request->get('tag')}_{$file->getClientOriginalName()}";
            $publicPath = public_path();
            $file->move("$publicPath/profilePictures/$fileOriginalName");
        }
        return $fileOriginalName;
    }

    private function createUser(Request $request, string $profilePicture): User
    {
        return (new UserRepository)->create([
            'name' => $request->get('name'),
            'tag' => $request->get('tag'),
            'email' => $request->get('email'),
            'profile_picture' => $profilePicture,
            'password' => Hash::make($request->get('password')),
        ]);
    }
}
