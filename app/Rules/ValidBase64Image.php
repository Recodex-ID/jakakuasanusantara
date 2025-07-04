<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBase64Image implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a valid base64 encoded image.');
            return;
        }

        // Check if it's valid base64
        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            $fail('The :attribute must be a valid base64 encoded string.');
            return;
        }

        // Check if it's a valid image
        $imageInfo = @getimagesizefromstring($decoded);
        if ($imageInfo === false) {
            $fail('The :attribute must be a valid image.');
            return;
        }

        // Check image type
        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedTypes)) {
            $fail('The :attribute must be a JPEG or PNG image.');
            return;
        }

        // Check image size (max 5MB)
        $maxSize = 5 * 1024 * 1024;
        if (strlen($decoded) > $maxSize) {
            $fail('The :attribute must not exceed 5MB in size.');
            return;
        }

        // Check minimum dimensions
        if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
            $fail('The :attribute must be at least 100x100 pixels.');
            return;
        }

        // Check maximum dimensions
        if ($imageInfo[0] > 4000 || $imageInfo[1] > 4000) {
            $fail('The :attribute must not exceed 4000x4000 pixels.');
            return;
        }
    }
}
