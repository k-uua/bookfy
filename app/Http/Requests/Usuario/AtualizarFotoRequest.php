<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarFotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'Selecione uma imagem.',
            'foto.image'    => 'O arquivo deve ser uma imagem.',
            'foto.mimes'    => 'Formatos aceitos: jpeg, png, webp.',
            'foto.max'      => 'A imagem deve ter no máximo 2 MB.',
        ];
    }
}
