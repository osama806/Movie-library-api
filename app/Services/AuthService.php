<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    use ResponseTrait;

    /**
     * Validation from user and generation token
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    public function login(array $data): Response
    {
        // check from request data
        $validator = Validator::make($data, [
            "email"             =>      [
                "required",
                "regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
            ],
            "password"          =>      "required|numeric|min_digits:6|max_digits:10"
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator, $this->getResponse("errors", $validator->errors(), 402));
        }
        // collection request data to array
        $dataValidation =  $validator->validated();
        try {
            // check email and password with saved in database
            if (!$token = JWTAuth::attempt($dataValidation)) {
                return $this->getResponse("error", "username or password is incorrect", 401);
            }
        } catch (JWTException $e) {
            return $this->getResponse("error", "Could not create token", 500);
        }
        return $this->getResponse("token", $token, 202);
    }

    /**
     * Create new user in database
     * @param array $data
     * @return Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function register(array $data): Response
    {
        $validator = Validator::make($data, [
            "name"              =>      "required|string|max:255",
            "email"             =>      [
                "required",
                "regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
                "unique:users,email"
            ],
            "password"          =>      "required|numeric|confirmed"
        ]);
        if ($validator->fails())
            throw new ValidationException($validator, $this->getResponse("errors", $validator->errors(), 402));
        $dataValidator = $validator->validated();
        // writing request data in database after validation
        $newUser = User::create([
            "name"          =>      $dataValidator['name'],
            "email"         =>      $dataValidator['email'],
            "password"      =>      bcrypt($dataValidator['password'])
        ]);
        return $newUser ?
            $this->getResponse("msg", "Registration is successfully", 201)
            : $this->getResponse("error", "Registration is failed!", 400);
    }

    /**
     * Revoke tokens for auth user
     * @return \Illuminate\Http\Response
     */
    public function logout(): Response
    {
        try {
            // Revoke tokens related to auth user
            JWTAuth::invalidate(JWTAuth::parseToken());
            return $this->getResponse("msg", "User logged out successfully", 200);
        } catch (JWTException $e) {
            throw new JWTException("Failed to logout, please try again", 500);
        }
    }
}
