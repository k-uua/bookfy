<?php

namespace App\Http\Requests\Postagem;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CriarPostagemRequest extends FormRequest
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
            'titulo'    => ['required', 'string', 'min:3', 'max:255'],
            'conteudo'  => ['required', 'string', 'min:5', 'max:5000'],
            'id_livro'  => ['nullable', 'integer', 'exists:livro,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'   => 'Informe um título para a postagem.',
            'titulo.min'        => 'O título deve ter pelo menos 3 caracteres.',
            'titulo.max'        => 'O título deve ter no máximo 255 caracteres.',
            'conteudo.required' => 'Escreva o conteúdo da postagem.',
            'conteudo.min'      => 'O conteúdo deve ter pelo menos 5 caracteres.',
            'conteudo.max'      => 'O conteúdo deve ter no máximo 5000 caracteres.',
            'id_livro.exists'   => 'O livro selecionado não existe no sistema.',
        ];
    }
}
