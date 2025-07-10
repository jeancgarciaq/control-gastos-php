<?php
/**
 * @file Controller.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Controlador base.
*/

namespace App\Controllers;

use PDO;

/**
 * Class Controller
 *
 * El controlador base del que todos los demás controladores de la aplicación heredan.
 * Proporciona una propiedad PDO para la interacción con la base de datos.
 *
 * @package App\Controllers
 */
abstract class Controller
{
    /**
     * @var PDO La instancia de la conexión a la base de datos.
     */
    protected PDO $pdo;

    /**
     * Constructor del controlador.
     *
     * Se encarga de inyectar la dependencia de la base de datos (PDO)
     * y la asigna a la propiedad $pdo para que esté disponible en los
     * controladores hijos.
     *
     * @param PDO $pdo La conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}