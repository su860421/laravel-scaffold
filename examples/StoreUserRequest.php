<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
        return [
            // Add validation rules here
            // Examples:
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users',
            // 'status' => 'sometimes|string|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Add custom messages here
            // Examples:
            // 'name.required' => 'Name is required',
            // 'email.required' => 'Email is required',
            // 'email.email' => 'Please enter a valid email format',
            // 'email.unique' => 'This email is already in use',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            // Add attribute names here
            // Examples:
            // 'name' => 'Name',
            // 'email' => 'Email',
            // 'status' => 'Status',
        ];
    }
}
