<?php

namespace App\Http\Requests\StockMovement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreStockMovementRequest extends FormRequest
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
                'required',
                'string',
                'uuid',
                Rule::exists('products', 'id')
            ],

            'type' => [
                'required',
                'string', 
                'in:stock-in,stock-out,adjustment',
            ],

            'quantity' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'max:99999999'
            ],

            'reason' => [
                'required',
                'string',
                'min:4', 
                'max:255'
            ],

            'references' => [
                'sometimes',
                'nullable',
                'required',
                'string',
                'min:4',
                'max:255'
            ],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.string' => 'Product ID must be a valid string.',
            'product_id.uuid' => 'Product ID format is invalid (must be a UUID).',
            'product_id.exists' => 'The selected product does not exist or has been deleted.',

            'type.required' => 'The movement type is required.',
            'type.string' => 'The movement type must be a valid string.',
            'type.in' => 'The movement type must be one of: stock-in, stock-out, or adjustment.',

            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be a whole number.',
            'quantity.min' => 'The quantity cannot be less than 0.',
            'quantity.max' => 'The quantity cannot exceed 99,999,999.',

            'reason.required' => 'A reason for this stock movement is required.',
            'reason.string' => 'The reason must be a valid string.',
            'reason.min' => 'The reason must be at least 4 characters long.',
            'reason.max' => 'The reason cannot exceed 255 characters.',

            'references.required' => 'The reference field is required when provided.',
            'references.string' => 'The reference must be a valid string.',
            'references.min' => 'The reference must be at least 4 characters long.',
            'references.max' => 'The reference cannot exceed 255 characters.',
        ];
    }
}
