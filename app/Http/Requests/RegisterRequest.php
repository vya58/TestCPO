<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'nullable|string|max:255',
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
            'login.max' => 'В поле Логин должно быть не более 255 символов.',
            'login.unique' => 'Пользователь с таким Логином уже существует.',
            'password.required'  => 'Поле Пароль обязательно для заполнения.',
            'first_name.required'  => 'Поле Пароль обязательно для заполнения.',
            'first_name.max' => 'В поле Имя должно быть не более 255 символов.',
            'last_name.required'  => 'Поле Пароль обязательно для заполнения.',
            'last_name.max' => 'В поле Фамилия должно быть не более 255 символов.',
        ];
    }

    public function validateWithErrors()
    {
        $output = [];
        $validator = Validator::make($this->request->all(), $this->rules(), $this->messages());
        if ($validator->fails()) {
            $output = $validator->messages();
        }

        return $output;
    }
}
