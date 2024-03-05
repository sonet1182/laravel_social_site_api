<?php

namespace App\Http\Requests\backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebPropertyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (request()->isMethod('POST')) {
            $titleRule = 'unique:web_properties';
        } elseif (request()->isMethod('PUT')) {
            $titleRule = Rule::unique('web_properties')->ignore(request()->web_property);
        }

        return [
            'title'  => ['required', 'string', $titleRule],
            'type'  => ['nullable', 'string'],
            'data'  => ['nullable', 'string'],
            'style'  => ['nullable', 'string'],
        ];
    }
}
