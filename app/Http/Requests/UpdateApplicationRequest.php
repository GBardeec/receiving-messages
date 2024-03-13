<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'params.id' => ['integer', 'required'],
            'params.comment' => ['string','required','max:255','min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.integer' => 'Поле c id должно быть числовым значением',
            'id.required' => 'Поле с id необходимо заполнить',

            'comment.required' => 'Поле c комментарией необходимо заполнить',
            'comment.max' => 'Максимальное значение комментария не должно превышать 255 символов',
            'comment.min' => 'Минимальное значение сообщения составляет 1 символ',
            'comment.string' => 'Поле с комментарией должно быть строковым значением',
        ];
    }
}
