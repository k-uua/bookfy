<?php

namespace App\Http\Requests\Postagem;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ComentarioPostagemRequest extends FormRequest
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
            'texto' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'texto.required' => 'Escreva algo antes de comentar.',
            'texto.min'      => 'O comentário deve ter pelo menos 3 caracteres.',
            'texto.max'      => 'O comentário deve ter no máximo 1000 caracteres.',
        ];
    }
}
