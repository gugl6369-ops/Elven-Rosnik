<?php

namespace App\Http\Requests\Account;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateAccountProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $username = $this->input('username');

        if (is_string($username)) {
            $this->merge(['username' => Str::lower(trim($username))]);
        }
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:64',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique(User::class, 'username')->ignore($this->user()->getKey()),
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
