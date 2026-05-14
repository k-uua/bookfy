<?php

namespace App\Http\Requests\Estante;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EstanteRequest extends FormRequest
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
            'nome' => ['required', 'string', 'min:2', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe um nome para a estante.',
            'nome.min'      => 'O nome deve ter pelo menos 2 caracteres.',
            'nome.max'      => 'O nome deve ter no máximo 255 caracteres.',
        ];
    }
}
