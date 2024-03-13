<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'params.id' => ['required', 'integer'],
            'params.comment' => ['required', 'string','max:255','min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'params.id.integer' => 'Поле c id должно быть числовым значением',
            'params.id.required' => 'Поле с id необходимо заполнить',

            'params.comment.required' => 'Поле c комментарией необходимо заполнить',
            'params.comment.max' => 'Максимальное значение комментария не должно превышать 255 символов',
            'params.comment.min' => 'Минимальное значение сообщения составляет 1 символ',
            'params.comment.string' => 'Поле с комментарией должно быть строковым значением',
        ];
    }
}
