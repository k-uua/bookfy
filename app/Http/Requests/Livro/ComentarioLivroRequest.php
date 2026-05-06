<?php

namespace App\Http\Requests\Livro;

use Illuminate\Foundation\Http\FormRequest;

class ComentarioLivroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // Comentário
            'texto'             => 'required|string|min:3|max:1000',

            // Identificação do livro na API
            'google_books_id'   => 'required|string|max:50',

            // Dados do livro para firstOrCreate caso ainda não exista localmente
            'titulo'            => 'required|string|max:255',
            'capa_livro_url'    => 'nullable|string',
            'descricao'         => 'nullable|string',
            'paginas'           => 'nullable|integer|min:1',
            'data_lancamento'   => 'nullable|string|max:20',
            'nota_google_books' => 'nullable|numeric|min:0|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'texto.required' => 'O comentário não pode estar vazio.',
            'texto.min'      => 'O comentário deve ter pelo menos 3 caracteres.',
            'texto.max'      => 'O comentário não pode ter mais de 1000 caracteres.',
        ];
    }
}
