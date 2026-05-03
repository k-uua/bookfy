<?php

namespace App\Http\Requests\Livro;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AvaliarLivroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_usuario' => 'required|exists:usuarios,id',
            'id_livro' => 'required|exists:livros,id',
            'nota' => 'required|numeric|min:0|max:5'
        ];
    }

    public function messages()
    {
        return [
            'id_usuario.required' => 'O campo ID do usuário é obrigatório.',
            'id_usuario.exists' => 'O usuário informado não existe.',
            'id_livro.required' => 'O campo ID do livro é obrigatório.',
            'id_livro.exists' => 'O livro informado não existe.',
            'nota.required' => 'O campo nota é obrigatório.',
            'nota.numeric' => 'A nota deve ser um número.',
            'nota.min' => 'A nota deve ser maior ou igual a 0.',
            'nota.max' => 'A nota deve ser menor ou igual a 5.'
        ];
    }
}
