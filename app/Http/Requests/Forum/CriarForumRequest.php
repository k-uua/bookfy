<?php

namespace App\Http\Requests\Forum;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CriarForumRequest extends FormRequest
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
            'nome'      => ['required', 'string', 'min:3', 'max:255'],
            'descricao' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'      => 'Informe um nome para o fórum.',
            'nome.min'           => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.max'           => 'O nome deve ter no máximo 255 caracteres.',
            'descricao.required' => 'Adicione uma descrição para o fórum.',
            'descricao.min'      => 'A descrição deve ter pelo menos 10 caracteres.',
            'descricao.max'      => 'A descrição deve ter no máximo 1000 caracteres.',
        ];
    }
}
