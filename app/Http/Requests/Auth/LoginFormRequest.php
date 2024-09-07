<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginFormRequest extends FormRequest
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
    public function prepareForValidation()
    {
        $this->merge([
            'email'         =>      strtolower(trim($this->input('email'))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email"             =>      "required|email",
            "password"          =>      "required|numeric|min_digits:6|max_digits:10"
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
            'isSuccess'     =>      false,
            'errors'        =>      $validator->errors()
        ], 422));
    }

    /**
     * Get custom attributes for validator errors.
     * @return string[]
     */
    public function attributes()
    {
        return [
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
            'email'         => 'The :attribute must be a format email.',
            'numeric'       => 'The :attribute must be a number.',
            'min_digits'    =>  'The :attribute must be a minimum :min_digits numbers',
            'max_digits'    =>  'The :attribute must be a maximum :max_digits numbers'
        ];
    }
}
