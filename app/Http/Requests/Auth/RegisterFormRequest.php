<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Prepare the data for validation.
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->input('name')),
            'email' => strtolower(trim($this->input('email'))),
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        return [
            "name"              =>      "required|string|max:255",
            "email"             =>      "required|email|unique:users,email",
            "password"          =>      "required|numeric|confirmed|min_digits:6|max_digits:10"
        ];
    }

    /**
     * Get exception for error inputs
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response([
            "isSuccess"     =>      false,
            "errors"        =>      $validator->errors()
        ], 422));
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'          => 'full name',
            'email'         => 'email address',
            'password'      => 'password',  // Add any other field you'd like
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required'      => 'The :attribute field is required.',
            'string'        => 'The :attribute must be a valid string.',
            'max'           => 'The :attribute must not exceed :max characters.',
            'unique'        => 'The :attribute has already been taken.',
            'numeric'       => 'The :attribute must be a number.',
            'confirmed'     => 'The :attribute confirmation does not match.',
            'min_digits'    =>  'The :attribute must be a minimum :min_digits numbers',
            'max_digits'    =>  'The :attribute must be a maximum :max_digits numbers'
        ];
    }
}
