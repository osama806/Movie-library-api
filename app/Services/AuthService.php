<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    use ResponseTrait;

    /**
     * Create new user in database
     * @param array $data
     * @return array
     */
    public function register(array $data)
    {
        $newUser = User::create([
            "name"          =>      $data['name'],
            "email"         =>      $data['email'],
            "password"      =>      bcrypt($data['password'])
        ]);
        return $newUser
            ? ['status'      =>   true]
            : ['status'     =>      false, 'msg'    =>  'Registration is failed!', 'code'  =>   400];
    }

    /**
     * Validation from user and generation token
     * @param array $data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array
     */
    public function login(array $data)
    {
        try {
            // check email and password with saved in database
            if (!$token = JWTAuth::attempt($data)) {
                return ['status'    =>  false, 'msg' => "username or password is incorrect", 'code' =>  401];
            }
        } catch (JWTException $e) {
            throw new HttpResponseException($this->getResponse('error', "Could not create token", 500));
        }
        return ['status'    =>  true, "token" =>  $token];
    }

    /**
     * Revoke tokens for auth user
     * @return array
     */
    public function logout()
    {
        try {
            // Check if token exists and invalidate it
            JWTAuth::invalidate(JWTAuth::getToken());
            return ['status'    =>  true];
        } catch (JWTException $e) {
            return [
                'status'    =>  false,
                'msg' => 'Failed to logout, please try again',
                'code'  =>  500
            ];
        }
    }
}
