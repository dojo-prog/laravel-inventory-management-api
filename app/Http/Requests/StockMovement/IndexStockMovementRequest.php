<?php

namespace App\Http\Requests\StockMovement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexStockMovementRequest extends FormRequest
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
            'product_id' => ['sometimes', 'string', 'uuid'],
            'type' => ['sometimes', 'string', 'in:stock-in,stock-out,adjustment'],
            'user_id' => ['sometimes', 'string', 'uuid'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date'],
            'sort_by' => ['sometimes', 'string', 'in:created_at'],
            'sort_order' => ['sometimes', 'string', 'in:desc,asc']
        ];
    }
}
