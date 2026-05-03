<?php

namespace App\Http\Requests\Estante;

use Illuminate\Foundation\Http\FormRequest;

class AdicionarLivroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome_estante'      => 'required|string|max:255',
            'google_books_id'   => 'required|string|max:50',
            'titulo'            => 'required|string|max:255',
            'capa_livro_url'    => 'nullable|string|max:500',
            'descricao'         => 'nullable|string',
            'paginas'           => 'nullable|integer|min:1',
            'data_lancamento'   => 'nullable|string|max:20',
            'nota_google_books' => 'nullable|numeric|min:0|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'nome_estante.required'    => 'Informe o nome da estante.',
            'google_books_id.required' => 'ID do livro não identificado.',
            'titulo.required'          => 'O título do livro é obrigatório.',
        ];
    }
}
