<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCoordinates implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value)) {
            $fail('The :attribute must be a valid coordinate.');
            return;
        }

        $coordinate = (float) $value;

        // Check if it's latitude
        if (str_contains($attribute, 'lat')) {
            if ($coordinate < -90 || $coordinate > 90) {
                $fail('The :attribute must be a valid latitude between -90 and 90 degrees.');
                return;
            }
        }
        
        // Check if it's longitude  
        if (str_contains($attribute, 'lng') || str_contains($attribute, 'lon')) {
            if ($coordinate < -180 || $coordinate > 180) {
                $fail('The :attribute must be a valid longitude between -180 and 180 degrees.');
                return;
            }
        }

        // Check for suspicious coordinates (exact 0,0 or obviously fake)
        if ($coordinate === 0.0) {
            $fail('The :attribute appears to be invalid (exact zero coordinates are not allowed).');
            return;
        }

        // Check precision (too many decimal places might indicate fake data)
        $decimalPlaces = strlen(substr(strrchr((string) $coordinate, "."), 1));
        if ($decimalPlaces > 8) {
            $fail('The :attribute has too much precision (maximum 8 decimal places allowed).');
            return;
        }
    }
}
