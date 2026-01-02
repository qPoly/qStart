<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganisationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'logo_path' => [
                'nullable',
            ],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht',
            'name.string' => 'Naam moet tekst zijn',
            'name.max' => 'Naam mag niet langer zijn dan 255 tekens',
        ];
    }
}
