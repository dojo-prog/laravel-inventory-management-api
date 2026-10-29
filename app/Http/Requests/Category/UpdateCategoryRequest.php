<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
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
                'sometimes',
                'required', 
                'string', 
                'min:2', 
                'max:50', 
                Rule::unique('categories')
                    ->where(function ($query) {
                        return $query->whereRaw(
                            'LOWER(name) = ?', 
                            [strtolower($this->name)]
                        );
                    })
                    ->ignore($this->route('category'))
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'min:10',
                'max:255'
            ]
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'name.required' => 'Category name is required',
            'name.string' => 'Category name must be a valid string',
            'name.min' => 'Category name must at least be :min characters long',
            'name.max' => 'Category name cannot exceed :max characters',
            'name.unique' => 'A category with this name already exists',

            'description.string' => 'Category description must be a valid string',
            'description.min' => 'Category description must at least be :min characters long',
            'description.max' => 'Category description cannot exceed :max characters',
        ];
    }
}
