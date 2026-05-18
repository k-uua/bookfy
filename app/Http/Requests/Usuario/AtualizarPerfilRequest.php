<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AtualizarPerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'       => ['required', 'string', 'max:255'],
            'email'      => [
                'required', 'email', 'max:255',
                Rule::unique('usuario', 'email')->ignore(Auth::id()),
            ],
            'senha_atual' => [
                'nullable', 'string',
                Rule::requiredIf(fn () => filled($this->input('nova_senha'))),
            ],
            'nova_senha'  => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'        => 'O nome é obrigatório.',
            'nome.max'             => 'O nome deve ter no máximo 255 caracteres.',
            'email.required'       => 'O e-mail é obrigatório.',
            'email.email'          => 'Informe um e-mail válido.',
            'email.unique'         => 'Este e-mail já está em uso por outra conta.',
            'senha_atual.required' => 'Informe a senha atual para definir uma nova.',
            'nova_senha.min'       => 'A nova senha deve ter pelo menos 8 caracteres.',
            'nova_senha.confirmed' => 'A confirmação da nova senha não confere.',
        ];
    }
}
