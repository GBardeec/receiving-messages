<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['string', 'required','max:25','min:3'],
            'password' => ['string', 'required','max:25','min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.string' => 'Поле логин должно быть строковым значением',
            'email.required' => 'Поле логин необходимо заполнить',
            'email.max' => 'Максимальное значение логина не должно превышать 25 символов',
            'email.min' => 'Минимальное значение логина составляет 3 символа',

            'password.string' => 'Поле пароль должно быть строковым значением',
            'password.required' => 'Поле пароль необходимо заполнить',
            'password.max' => 'Максимальное значение пароля не должно превышать 25 символов',
            'password.min' => 'Минимальное значение пароля составляет 3 символа',
        ];
    }
}
