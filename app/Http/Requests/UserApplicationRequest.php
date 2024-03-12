<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['string', 'required','max:255','min:3'],
            'name' => ['string', 'required','max:255'],
            'message' => ['required','max:255','min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.string' => 'Поле почты должно быть строковым значением',
            'email.required' => 'Поле почты необходимо заполнить',
            'email.max' => 'Максимальное значение логина не должно превышать 255 символов',
            'email.min' => 'Минимальное значение логина составляет 3 символа',

            'name.string' => 'Поле имени должно быть строковым значением',
            'name.required' => 'Поле имени необходимо заполнить',
            'name.max' => 'Максимальное значение имени не должно превышать 255 символов',

            'message.required' => 'Поле c сообщением необходимо заполнить',
            'message.max' => 'Максимальное значение сообщения не должно превышать 255 символов',
            'message.min' => 'Минимальное значение сообщения составляет 3 символа',
        ];
    }
}
