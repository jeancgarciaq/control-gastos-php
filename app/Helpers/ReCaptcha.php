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
 *
 * Esta clase centraliza la lógica para interactuar con la API de Google reCAPTCHA,
 * incluyendo la obtención de claves desde variables de entorno y la validación
 * de tokens de respuesta del lado del servidor.
 */
class ReCaptcha
{
    /**
     * Obtiene la Site Key (clave pública) de reCAPTCHA desde las variables de entorno.
     *
     * @return string La Site Key para ser usada en el lado del cliente (frontend). Retorna una cadena vacía si no se encuentra.
     */
    public static function getSiteKey(): string
    {
        // Usar $_ENV es más directo con la librería vlucas/phpdotenv.
        return $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
    }

    /**
     * Obtiene la Secret Key (clave privada) de reCAPTCHA desde las variables de entorno.
     *
     * @return string La Secret Key para la verificación en el lado del servidor. Retorna una cadena vacía si no se encuentra.
     */
    public static function getSecretKey(): string
    {
        return $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
    }

    /**
     * Verifica el token de respuesta de reCAPTCHA con la API de Google usando cURL.
     *
     * @param string $token El token 'g-recaptcha-response' recibido del cliente.
     * @return bool Devuelve true si la verificación es exitosa y Google confirma la validez del token, de lo contrario devuelve false.
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
        
        // Incluir la IP del usuario mejora la seguridad y el análisis de Google.
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $params['remoteip'] = $_SERVER['REMOTE_ADDR'];
        }
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        // Se utiliza cURL por ser más robusto que file_get_contents para peticiones de red.
        $ch = \curl_init($url);
        \curl_setopt($ch, CURLOPT_POST, true);
        \curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Timeout de conexión en segundos
        \curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // Timeout total de la petición en segundos

        $response = \curl_exec($ch);

        if (\curl_errno($ch)) {
            // Si hay un error de cURL (ej. no se puede conectar), la verificación falla.
            error_log('cURL error on reCAPTCHA verification: ' . \curl_error($ch));
            \curl_close($ch);
            return false;
        }

        \curl_close($ch);

        $result = json_decode($response, true);

        // La verificación es exitosa solo si 'success' es true en la respuesta de Google.
        // Opcional: Podrías añadir una comprobación del 'score' aquí si lo deseas.
        // ej. return isset($result['success']) && $result['success'] === true && $result['score'] > 0.5;
        return isset($result['success']) && $result['success'] === true;
    }
}