<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreProductRequest extends FormRequest
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
            'category_id' => [
                'required',
                'string',
                Rule::exists('categories', 'id')
            ],

            'sku' => [
                'sometimes',
                'nullable',
                'string', 
                'min:4',
                'max:50'
            ],

            'name' => [
                'required',
                'string',
                'min:2',
                'max:100'
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'min:10',
                'max:300'
            ],

            'unit' => [
                'required',
                'string',
                'min:2',
                'max:50'
            ],

            'unit_cost' => [
                'bail',    
                'required',
                'numeric',
                'decimal:2',
                'min:1',
                'max:99999999'
            ],

            'stock_quantity' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'max:99999999',
            ],

            'reorder_level' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'max:999',
            ],

            'status' => [
                'required',
                'string',
                'in:active,inactive'
            ]
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required',
            'category_id.string' => 'Category must be a valid string',
            'category_id.exists' => 'The category provided does not exist or has been currently deleted',
            
            'sku.string' => 'Product SKU must be a valid string',
            'sku.min' => 'Product SKU must at least be :min characters long',
            'sku.max' => 'Product SKU cannot exceed :max characters',

            'name.required' => 'Product name is required',
            'name.string' => 'Product name must be a valid string',
            'name.min' => 'Product name must at least be :min characters long',
            'name.max' => 'Product name cannot exceed :max characters',

            'description.string' => 'Product description must be a valid string',
            'description.min' => 'Product description must at least be :min characters long',
            'description.max' => 'Product description cannot exceed :max characters',

            'unit.string' => 'Product unit of measurement must be a valid string',
            'unit.min' => 'Product unit of measurement must at least be :min characters long',
            'unit.max' => 'Product unit of measurement cannot exceed :max characters',

            'unit_cost.required' => 'Product unit cost is required',
            'unit_cost.numeric' => 'Product unit cost must be a number',
            'unit_cost.decimal' => 'Product unit cost can have 2 decimal places at most',
            'unit_cost.min' => 'Product unit cost value must at least be :min',
            'unit_cost.max' => 'Product unit cost cannot exceed :max',

            'stock_quantity.required' => 'Product initial stock quantity is required', 
            'stock_quantity.integer' => 'Product stock quantity must be a whole number', 
            'stock_quantity.min' => 'Product stock quantity must be at least :min', 
            'stock_quantity.max' => 'Product stock quantity cannot exceed :max', 

            'reorder_level.required' => 'Product reorder level is required',
            'reorder_level.integer' => 'Product reorder level must be a whole number',
            'reorder_level.min' => 'Product reorder level must be at least :min',
            'reorder_level.max' => 'Product reorder level cannot exceed :max',

            'status.required' => 'Product status is required',
            'status.string' => 'Product status must be a valid string',
            'status.in' => 'Product status must be either active or inactive',
        ];
    }
}
