<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ProductRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;
        
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name,' . $productId,
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $productId,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_urls' => 'nullable|string',
            // Backward compatibility
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category does not exist.',
            'name.required' => 'The product name is required.',
            'name.unique' => 'A product with this name already exists.',
            'slug.unique' => 'A product with this slug already exists.',
            'description.required' => 'The product description is required.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka yang valid.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.required' => 'The stock quantity is required.',
            'stock.integer' => 'The stock must be a whole number.',
            'stock.min' => 'The stock cannot be negative.',
            'weight.numeric' => 'The weight must be a valid number.',
            'weight.min' => 'The weight cannot be negative.',
            'images.array' => 'Images must be provided as an array.',
            'images.max' => 'You can upload maximum 10 images.',
            'images.*.image' => 'Each file must be a valid image.',
            'images.*.mimes' => 'Each image must be a JPEG, PNG, JPG, or GIF file.',
            'images.*.max' => 'Each image must not be larger than 2MB.',
            'image.image' => 'The image must be a valid image file.',
            'image.mimes' => 'The image must be a JPEG, PNG, JPG, or GIF file.',
            'image.max' => 'The image must not be larger than 2MB.',
            'image_url.url' => 'The image URL must be a valid URL.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('slug') && $this->has('name')) {
            $this->merge([
                'slug' => Str::slug($this->name)
            ]);
        }
    }
}
