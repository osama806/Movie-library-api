<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Services\AuthService;
use App\Traits\ResponseTrait;

class AuthController extends Controller
{
    use ResponseTrait;
    protected $authService;
    /**
     * Call AuthService when this controller is running
     * @param \App\Services\AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }



    /**
     * Create new user in database
     * @param \App\Http\Requests\Auth\RegisterFormRequest $registerFormRequest
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterFormRequest $registerFormRequest)
    {
        $validated = $registerFormRequest->validated();
        $response = $this->authService->register($validated);
        return $response['status']
            ? $this->getResponse('msg', 'Registration is successfully', 201)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Validation from user and generation token
     * @param \App\Http\Requests\Auth\LoginFormRequest $loginFormRequest
     * @return \Illuminate\Http\Response
     */
    public function login(LoginFormRequest $loginFormRequest)
    {
        $validated = $loginFormRequest->validated();
        $response = $this->authService->login($validated);
        return $response['status']
            ? $this->getResponse('token', $response['token'], 202)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }


    /**
     * Revoke tokens for auth user
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $response = $this->authService->logout();
        return $response['status']
            ? $this->getResponse('msg', 'User logged out successfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }
}
