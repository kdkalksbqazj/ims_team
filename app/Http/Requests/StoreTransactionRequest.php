<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Transaction::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:in,out,transfer,adjustment',
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'to_branch_id' => [
                'nullable',
                'exists:branches,id',
                'required_if:type,transfer',
                'prohibited_unless:type,transfer',
                'different:branch_id',
            ],
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}