<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    /**
     * Регистрация пользователя
     *
     * @param  RegisterRequest  $request
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function register(RegisterRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $params = $request->safe();

        $params['password'] = Hash::make($params['password']);

        $registerData = $params->toArray();

        $user = User::create($registerData);

        $token = $user->createToken('auth-token');

        $data = [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
        return new ApiSuccessResponse($data, Response::HTTP_CREATED);
    }

    /**
     * Авторизация пользователя
     *
     * @param  LoginRequest  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function login(LoginRequest $request): ApiSuccessResponse|ApiErrorResponse
    {

        if (!Auth::attempt($request->validated(), true)) {
            abort(Response::HTTP_UNAUTHORIZED, trans('auth.failed'));
        }

        $user = User::where('login', $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            abort(Response::HTTP_UNAUTHORIZED, trans('auth.failed'));
        }

        $token = $user->createToken('auth-token');

        $data = ['token' => $token->plainTextToken];
        return new ApiSuccessResponse($data);
    }

    /**
     * Выход пользователяиз системы
     * В ТЗ не указано, но как-бы логично..
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function logout(): ApiSuccessResponse|ApiErrorResponse
    {
        Auth::user()->tokens()->delete();

        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
