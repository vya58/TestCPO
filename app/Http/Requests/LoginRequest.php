<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array <string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|string',
            'password' => 'required|string',
        ];
    }

    /**
     * Сообщения об ошибках валидации
     *
     */
    public function messages()
    {
        return [
            'login.required' => 'Поле Логин обязательно для заполнения.',
            'password.required'  => 'Поле Пароль обязательно для заполнения',
        ];
    }
}
