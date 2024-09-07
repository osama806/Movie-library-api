<?php

namespace App\Http\Requests\Movie;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexFormRequest extends FormRequest
{
    use ResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        return [
            "per_page"          =>      "nullable|integer|min:1",
            "sort_order"        =>      "nullable|string|in:asc,desc",
            "director"          =>      "nullable|string"
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
        throw new HttpResponseException($this->getResponse('errors', $validator->errors(), 422));
    }

    /**
     * Get custom attributes for validator errors.
     * @return array
     */
    public function attributes(): array
    {
        return [
            'per_page'      => 'items per page',
            'sort_order'    => 'sorting order',
            'director'      => 'director name',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     * @return array
     */
    public function messages(): array
    {
        return [
            'integer'       => 'The :attribute must be a valid integer.',
            'min'           => 'The :attribute must be at least :min.',
            'in'            => 'The :attribute must be either "asc" or "desc".',
            'string'        => 'The :attribute must be a valid string.',
        ];
    }
}
