<?php

namespace App\Http\Requests\Forum;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CriarTopicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'Informe um título para o tópico.',
            'titulo.min'      => 'O título deve ter pelo menos 3 caracteres.',
            'titulo.max'      => 'O título deve ter no máximo 255 caracteres.',
        ];
    }
}
