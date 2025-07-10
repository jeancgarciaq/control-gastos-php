<?php

namespace App\Helpers;

class ReCaptcha
{
    /**
     * @return string la site-key para inyectar en el formulario.
    */
    public static function getSiteKey(): string
    {
        return $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
    }

    /**
     * @return string la secret-key para la verificación en server-side.
    */
    public static function getSecretKey(): string
    {
        return $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
    }

    /**
     * Verifica el token con la API de Google reCAPTCHA v3.
     *
     * @param string      $token     El token recibido del cliente.
     * @param string|null $secretKey La clave secreta (opcional).
     * @return bool
     */
    public static function verify(string $token, ?string $secretKey = null): bool
    {
        $secretKey = $secretKey ?? getenv('RECAPTCHA_SECRET_KEY');
        $remoteIp  = $_SERVER['REMOTE_ADDR'] ?? null;
        $url = 'https://www.google.com/recaptcha/api/siteverify'
             . '?secret=' . urlencode($secretKey)
             . '&response=' . urlencode($token)
             . ($remoteIp ? '&remoteip=' . urlencode($remoteIp) : '');

        $response = @file_get_contents($url);
        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);
        // Opcional: chequear score y action según tu config
        return isset($result['success']) && $result['success'] === true;
    }
}