<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponse;

class LoginRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool // Penting: tambahkan return type declaration
    {
        return true; // <<<--- Ini yang BENAR untuk LoginRequest
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse($validator->errors()->all(), 422)
        );
    }
}
