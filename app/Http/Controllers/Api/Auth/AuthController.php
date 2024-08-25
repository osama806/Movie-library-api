<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

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
     * Validation from user and generation token
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        return $this->authService->login($request->all());
    }


    /**
     * Create new user in database
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        return $this->authService->register($request->all());
    }

    /**
     * Revoke tokens for auth user
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        return $this->authService->logout();
    }
}
