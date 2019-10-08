<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'profile_picture' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'tag' => ['required', 'string', 'max:60', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * @param array $data
     * @param Request $request
     * @return mixed
     */
    protected function create(array $data, Request $request)
    {
        return User::create([
            'name' => $data['name'],
            'tag' => $data['tag'],
            'email' => $data['email'],
            'profilePicture' => $this->storeProfilePicture($request),
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function storeProfilePicture(Request $request): string
    {
        $fileOriginalName = 'profile.png';
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileOriginalName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('profilePictures').'/', $fileOriginalName);
        }
        return $fileOriginalName;
    }
}
