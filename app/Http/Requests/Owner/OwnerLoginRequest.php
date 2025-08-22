<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponse;

class OwnerLoginRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool
    {
        return true;
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
            'password' => 'required|string|min:6|max:255',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username harus diisi',
            'username.string' => 'Username harus berupa teks',
            'username.max' => 'Username maksimal 255 karakter',
            'password.required' => 'Password harus diisi',
            'password.string' => 'Password harus berupa teks',
            'password.min' => 'Password minimal 6 karakter',
            'password.max' => 'Password maksimal 255 karakter',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse($validator->errors()->all(), 422)
        );
    }
}