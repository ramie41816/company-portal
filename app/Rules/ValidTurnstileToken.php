<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ValidTurnstileToken implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! config('services.turnstile.enabled')) {
            return;
        }

        if (! is_string($value) || $value === '') {
            $fail('Please complete the security verification.');
            return;
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post(config('services.turnstile.verify_url'), [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

        if (! $response->ok() || ! $response->json('success')) {
            $fail('Security verification failed. Please try again.');
        }
    }
}
