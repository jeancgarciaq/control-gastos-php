<?php
/**
 * @file ReCaptcha.php
 * @package App\Helpers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Proporciona métodos de ayuda para la integración con Google reCAPTCHA v3.
 */

namespace App\Helpers;

/**
 * @class ReCaptcha
 * @brief Gestiona la obtención de claves y la verificación de tokens de reCAPTCHA v3.
 */
class ReCaptcha
{
    /**
     * Obtiene la Site Key (clave pública) de reCAPTCHA desde las variables de entorno.
     * @return string La Site Key para ser usada en el lado del cliente (frontend).
     */
    public static function getSiteKey(): string
    {
        return $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
    }

    /**
     * Obtiene la Secret Key (clave privada) de reCAPTCHA desde las variables de entorno.
     * @return string La Secret Key para la verificación en el lado del servidor.
     */
    public static function getSecretKey(): string
    {
        return $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
    }

    /**
     * Verifica el token de respuesta de reCAPTCHA con la API de Google usando cURL.
     * @param string $token El token 'g-recaptcha-response' recibido del cliente.
     * @return bool Devuelve true si la verificación es exitosa, de lo contrario false.
     */
    public static function verify(string $token): bool
    {
        $secretKey = self::getSecretKey();

        if (empty($token) || empty($secretKey)) {
            return false;
        }

        $params = [
            'secret'   => $secretKey,
            'response' => $token,
        ];
        
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $params['remoteip'] = $_SERVER['REMOTE_ADDR'];
        }
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        /**
         * CORRECCIÓN: Se añade una barra invertida (\) antes de cada función cURL
         * para indicar que pertenecen al espacio de nombres global de PHP.
         */
        $ch = \curl_init($url);
        \curl_setopt($ch, CURLOPT_POST, true);
        \curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = \curl_exec($ch);

        if (\curl_errno($ch)) {
            error_log('cURL error on reCAPTCHA verification: ' . \curl_error($ch));
            \curl_close($ch);
            return false;
        }

        \curl_close($ch);

        $result = json_decode($response, true);

        return isset($result['success']) && $result['success'] === true;
    }
}