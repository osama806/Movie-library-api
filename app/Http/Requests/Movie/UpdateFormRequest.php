<?php

namespace App\Http\Requests\Movie;

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
            "title"         => "nullable|string|max:255",
            "director"      => "nullable|string|max:255",
            "genre"         => "nullable|string|max:255",
            "release_year"  => "nullable|digits:4|integer|min:1800|max:" . date('Y'),
            "description"   => "nullable|string",
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
            'title'         => 'movie title',
            'director'      => 'director name',
            'genre'         => 'genre',
            'release_year'  => 'release year',
            'description'   => 'movie description',
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
            'string'           => 'The :attribute must be a valid string.',
            'max'              => 'The :attribute may not be greater than :max characters.',
            'digits'           => 'The :attribute must be exactly :digits digits.',
            'integer'          => 'The :attribute must be an integer.',
            'min'              => 'The :attribute must be at least :min.',
            'release_year.max' => 'The :attribute cannot be in the future.',
        ];
    }
}
