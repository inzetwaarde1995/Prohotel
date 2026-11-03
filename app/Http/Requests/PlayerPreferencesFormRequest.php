<?php

namespace App\Http\Requests;

use App\Emulator\Data\PlayerPreference;
use Illuminate\Foundation\Http\FormRequest;

class PlayerPreferencesFormRequest extends FormRequest
{
    protected $errorBag = 'preferences';

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $rules = [];

        foreach (PlayerPreference::cases() as $preference) {
            $rules[$preference->value] = ['sometimes', 'boolean'];
        }

        return $rules;
    }

    /**
     * @return array<value-of<PlayerPreference>, bool>
     */
    public function preferences(): array
    {
        return array_map(fn (mixed $value): bool => (bool) $value, $this->validated());
    }
}
