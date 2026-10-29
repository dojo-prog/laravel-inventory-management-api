<?php

namespace App\Http\Requests\ProductSupplier;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateProductSupplierRequest extends FormRequest
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
            'product_id' => [
                'sometimes',
                'required',
                'string',
                'uuid',
                Rule::exists('products', 'id')
            ],

            'supplier_id' => [
                'sometimes',
                'required',
                'string',
                'uuid',
                Rule::exists('suppliers', 'id')
            ],

            'supplier_sku' => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:50'
            ],

            'unit_cost' => [
                'bail',
                'sometimes',
                'nullable',
                'numeric',
                'decimal:2',
                'min:0',
                'max:99999999'
            ]
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'product_id.required' => 'Product is required',
            'product_id.string' => 'Product ID must be a valid string',
            'product_id.uuid' => 'Product ID format is invalid (must be a UUID)',

            'supplier_id.required' => 'Supplier is required',
            'supplier_id.string' => 'Supplier ID must be a valid string',
            'supplier_id.uuid' => 'Supplier ID format is invalid (must be a UUID)',
            
            'supplier_sku.string' => 'Supplier SKU must be a valid string',
            'supplier_sku.min' => 'Supplier SKU must at least be :min characters long',
            'supplier_sku.max' => 'Supplier SKU cannot exceed :max characters',

            'unit_cost.numeric' => 'Aquisition unit cost must be a number',
            'unit_cost.decimal' => 'Aquisition unit cost can have 2 decimal places max',
            'unit_cost.min' => 'Aquisition unit cost must be a positive number',
            'unit_cost.max' => 'Aquisition unit cost cannot exceed :max',
        ];
    }
}
