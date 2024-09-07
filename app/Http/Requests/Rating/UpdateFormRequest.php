<?php

namespace App\Http\Requests\Rating;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFormRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "movie_id"      =>      "nullable|integer",
            "rating"        =>      "nullable|numeric|digits:1|min:1|max:5",
            "review"        =>      "nullable|string",
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
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'movie_id' => 'movie',
            'rating'   => 'rating',
            'review'   => 'review',
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
            'integer'    => 'The :attribute must be a valid integer.',
            'numeric'    => 'The :attribute must be a number.',
            'digits'     => 'The :attribute must be a single digit.',
            'min'        => 'The :attribute must be at least :min.',
            'max'        => 'The :attribute must not exceed :max.',
            'string'     => 'The :attribute must be a valid string.',
        ];
    }
}
