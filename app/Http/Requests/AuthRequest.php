<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['string', 'required','max:255','min:3'],
            'password' => ['required','max:255','min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.string' => 'Поле почты должно быть строковым значением',
            'email.required' => 'Поле почты необходимо заполнить',
            'email.max' => 'Максимальное значение логина не должно превышать 255 символов',
            'email.min' => 'Минимальное значение логина составляет 3 символа',

            'password.required' => 'Поле пароль необходимо заполнить',
            'password.max' => 'Максимальное значение пароля не должно превышать 255 символов',
            'password.min' => 'Минимальное значение пароля составляет 3 символа',
        ];
    }
}
