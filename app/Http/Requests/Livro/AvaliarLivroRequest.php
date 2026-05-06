<?php

namespace App\Http\Requests\Livro;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AvaliarLivroRequest extends FormRequest
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
            'google_books_id'   => ['required', 'string'],
            'titulo'            => ['required', 'string', 'max:500'],
            'capa_livro_url'    => ['nullable', 'string'],
            'descricao'         => ['nullable', 'string'],
            'paginas'           => ['nullable', 'integer'],
            'data_lancamento'   => ['nullable', 'string'],
            'nota_google_books' => ['nullable', 'numeric'],
            'nota'              => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'nota.required' => 'Selecione uma nota entre 1 e 5 estrelas.',
            'nota.min'      => 'A nota mínima é 1 estrela.',
            'nota.max'      => 'A nota máxima é 5 estrelas.',
        ];
    }
}
