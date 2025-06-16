<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
        $user = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user),
            ],
        ];

        // Password is required for new users, optional for updates
        if (! $user) {
            $rules['password'] = ['required', Password::defaults(), 'confirmed'];
        } else {
            $rules['password'] = ['nullable', Password::defaults(), 'confirmed'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Naam is verplicht',
            'name.string' => 'Naam moet tekst zijn',
            'name.max' => 'Naam mag niet langer zijn dan 255 tekens',

            'email.required' => 'E-mailadres is verplicht',
            'email.string' => 'E-mailadres moet tekst zijn',
            'email.email' => 'E-mailadres moet een geldig e-mailadres zijn',
            'email.max' => 'E-mailadres mag niet langer zijn dan 255 tekens',
            'email.unique' => 'E-mailadres is al in gebruik',

            'password.required' => 'Wachtwoord is verplicht',
            'password.confirmed' => 'Wachtwoorden komen niet overeen',
            'password.min' => 'Wachtwoord moet minimaal 8 tekens bevatten',
            'password.mixed' => 'Wachtwoord moet minimaal één hoofdletter en één kleine letter bevatten',
            'password.numbers' => 'Wachtwoord moet minimaal één cijfer bevatten',
            'password.symbols' => 'Wachtwoord moet minimaal één speciaal teken bevatten',
        ];
    }
}
