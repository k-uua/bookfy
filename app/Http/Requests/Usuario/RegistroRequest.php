<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'senha' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'  => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'email.unique'   => 'Este e-mail já está cadastrado.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.min'      => 'A senha deve ter pelo menos 8 caracteres.',
            'senha.confirmed' => 'As senhas não coincidem.',
        ];
    }
}
