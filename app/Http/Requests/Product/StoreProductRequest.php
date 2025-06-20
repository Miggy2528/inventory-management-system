<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class StoreProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'              => 'required|string',
            'slug'              => 'required|unique:products',
            'category_id'       => 'required|integer',
            'unit_id'           => 'required|integer',
            'meat_cut_id'       => 'required|integer|exists:meat_cuts,id',
            'quantity'          => 'required|integer|min:0',
            'weight_per_unit'   => 'required|numeric|min:0',
            'price_per_kg'      => 'required|numeric|min:0',
            'total_weight'      => 'required|numeric|min:0',
            'storage_location'  => 'required|string',
            'expiration_date'   => 'required|date|after:today',
            'processing_date'   => 'required|date|before_or_equal:today',
            'source'            => 'required|string',
            'grade'             => 'required|string',
            'notes'             => 'nullable|string|max:1000'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name, '-'),
            'total_weight' => ($this->weight_per_unit ?? 0) * ($this->quantity ?? 0)
        ]);
    }

    public function messages(): array
    {
        return [
            'expiration_date.after' => 'The expiration date must be a future date.',
            'processing_date.before_or_equal' => 'The processing date cannot be in the future.',
            'weight_per_unit.min' => 'The weight per unit must be greater than 0.',
            'price_per_kg.min' => 'The price per kilogram must be greater than 0.',
            'total_weight.min' => 'The total weight must be greater than 0.',
        ];
    }
}
