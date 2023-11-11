<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AuthRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required!',
            'name.string' => 'Name must be a string!',
            'email.required' => 'Email is required!',
            'email.email' => 'Email is invalid!',
            'email.unique' => 'Email is already taken!',
            'password.required' => 'Password is required!',
            'password.string' => 'Password must be a string!',
            'password.min' => 'Password must be at least 8 characters!',
        ];
    }

    /**
     * Handle a failed validation attempt.
     * 
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    public function failedValidation(Validator $validator): void
    {
        $response = [
            'status' => 'failed',
            'message' => $validator->errors()->first(),
        ];
        
        throw new ValidationException($validator, response()->json($response, 422));
    }
}
