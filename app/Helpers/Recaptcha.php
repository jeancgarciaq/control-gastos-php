<?php

namespace App\Helpers;

class ReCaptcha
{
    /**
     * Verifies the Google reCAPTCHA response.
     *
     * @param string $response The reCAPTCHA response token.
     * @param string $secretKey The reCAPTCHA secret key.
     * @return bool True if the response is valid, false otherwise.
     */
    public static function verify(string $response, string $secretKey): bool
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $response,
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            // Handle the error (e.g., log it)
            return false;
        }

        $response = json_decode($result, true);

        return $response['success'] ?? false;
    }
}