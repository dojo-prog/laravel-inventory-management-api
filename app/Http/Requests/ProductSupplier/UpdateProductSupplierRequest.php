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
            'supplier_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists('suppliers', 'id'),
                Rule::unique('product_suppliers', 'supplier_id')
                    ->where(function ($query) {
                        return $query->where('product_id', $this->product_id);
                    })
                    ->ignore($this->route('productSupplier'))
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
            'supplier_id.required' => 'Supplier is required',
            'supplier_id.string' => 'Supplier ID must be a valid string',
            'supplier_id.uuid' => 'Supplier ID format is invalid (must be a UUID)',
            'supplier_id.exists' => 'Supplier provided does not exist or has been deleted',
            'supplier_id.unique' => 'This product is already registered/associated with this supplier',
            
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
