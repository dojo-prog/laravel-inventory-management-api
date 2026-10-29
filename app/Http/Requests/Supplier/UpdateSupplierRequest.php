<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateSupplierRequest extends FormRequest
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
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('suppliers', 'name')
                    ->where(function ($query) {
                        return $query->whereRaw(
                            'LOWER(contact_person) = ?', 
                            [strtolower($this->contact_person)]
                        );
                    })
                    ->ignore($this->route('supplier'))
            ],

            'contact_person' => [
                'sometimes',
                'nullable',
                'string',
                'min:2',
                'max:100'
            ],

            'email' => [
                'sometimes',
                'nullable',
                'string',
                'email',
                'max:255'
            ],

            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'digits:11',              
                'regex:/^09[0-9]{9}$/'    
            ],

            'address' => [
                'sometimes',
                'nullable',
                'string', 
                'min:8',
                'max:300'
            ]
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'name.required' => 'Supplier name is required',
            'name.string' => 'Supplier name must be a valid string',
            'name.min' => 'Supplier name must be at least :min characters long',
            'name.max' => 'Supplier name cannot exceed :max characters',
            'name.unique' => 'A supplier with this name and contact person already exists',

            'contact_person.string' => 'Contact person name must be a valid string',
            'contact_person.min' => 'Contact person name must be at least :min characters long',
            'contact_person.max' => 'Contact person name cannot exceed :max characters',

            'email.string' => 'Supplier email must be a valid string',
            'email.email' => 'Invalid email format',
            'email.max' => 'Supplier email cannot exceed :max characters',

            'phone.string' => 'Phone number value must be a valid string',
            'phone.digits' => 'Invalid PH phone number',
            'phone.regex' => 'Invalid PH phone number (e.g 09123456789)',

            'address.string' => 'Supplier address must be a valid string',
            'address.min' => 'Supplier address must be at least :min characters long',
            'address.max' => 'Supplier address cannot exceed :max characters',
        ];
    }
}
