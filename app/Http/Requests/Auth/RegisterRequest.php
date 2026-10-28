<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class RegisterRequest extends FormRequest
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
            'name' => [
                'bail',
                'required',
                'string', 
                'min:2', 
                'max:100', 
                Rule::unique('users', 'name')
            ],

            'email' => [
                'bail',
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'confirmed',
            ]
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'name.required' => 'Full name is required',
            'name.string' => 'Full name must be a valid string',
            'name.min' => 'Full name must at least be :min characters long',
            'name.max' => 'Full name cannot exceed :max characters',
            'name.unique' => 'A user with this name already exists',

            'email.required' => 'Email is required',
            'email.string' => 'Email must be a valid string',
            'email.email' => 'Invalid email format',
            'email.max' => 'Email cannot exceed :max characters',
            'email.unique' => 'A user with this email already exists',

            'password.required' => 'Password is required',
            'password.string' => 'Password must be a valid string',
            'password.min' => 'Password must at least be :min characters long',
            'password.max' => 'Password cannot exceed :max characters',
            'password.confirmed' => 'Passwords do not match',

        ];
    }
}
