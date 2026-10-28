<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
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
            'page' => ['sometimes', 'integer'],
            'per_page' => ['sometimes', 'integer'],
            'search' => ['sometimes', 'nullable', 'string'],
            'category_id' => ['sometimes', 'string', 'uuid'],
            'supplier_id' => ['sometimes', 'string', 'uuid'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
            'min_stock' => ['sometimes', 'integer'],
            'max_stock' => ['sometimes', 'integer'],
            'low_stock' => ['sometimes', 'boolean'],
            'sort_by' => ['sometimes', 'string', 'in:created_at,name,unit_cost,stock_quantity'],
            'sort_order' => ['sometimes', 'string', 'in:desc,asc'],
        ];
    }
}
