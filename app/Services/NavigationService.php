<?php
/**
 * @file NavigationService.php
 * @package App\Services
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-11
 * @brief Servicio para gestionar el estado de la navegación y el contexto de la URL.
 */

namespace App\Services;

/**
 * @class NavigationService
 * @brief Proporciona lógica para analizar la URI de la solicitud y determinar el contexto de navegación.
 *
 * Esta clase se encarga de identificar la entidad principal (ej. 'profiles', 'incomes') y el ID
 * del recurso que se está visitando. Ofrece métodos para que las vistas puedan mostrar
 * dinámicamente enlaces y estados activos, manteniendo la lógica fuera de la capa de presentación.
 */
class NavigationService
{
    /**
     * @var string La URI completa de la solicitud, sin slashes al inicio/final.
     */
    private string $currentUri;

    /**
     * @var string|null La entidad principal detectada en la URI (ej. 'profiles').
     */
    private ?string $currentEntity = null;

    /**
     * @var int|null El ID numérico del recurso detectado en la URI.
     */
    private ?int $currentId = null;

    /**
     * @var array Lista de entidades válidas que el servicio puede reconocer.
     */
    private array $validEntities = ['profiles', 'incomes', 'expenses'];

    /**
     * Constructor del NavigationService.
     *
     * @param string $requestUri La URI de la solicitud actual (ej. de $_SERVER['REQUEST_URI']).
     */
    public function __construct(string $requestUri)
    {
        $this->currentUri = trim($requestUri, '/');
        $this->parseUri();
    }

    /**
     * Analiza la URI para extraer la entidad y el ID.
     * Es un método privado que se llama automáticamente durante la instanciación.
     *
     * @return void
     */
    private function parseUri(): void
    {
        $uriParts = explode('/', $this->currentUri);

        // Identificar la entidad comparando con la lista de entidades válidas.
        if (isset($uriParts[0]) && in_array($uriParts[0], $this->validEntities)) {
            $this->currentEntity = $uriParts[0];
        }

        // Identificar el ID si es la segunda parte de la URI y es numérico.
        if (isset($uriParts[1]) && is_numeric($uriParts[1])) {
            $this->currentId = (int) $uriParts[1];
        }
    }

    /**
     * Devuelve el contexto de navegación actual como un objeto.
     *
     * @return object Un objeto que contiene la entidad, el ID y el nombre de la entidad en singular.
     *                Ej: { entity: 'profiles', id: 15, entityNameSingular: 'Profile' }
     */
    public function getContext(): object
    {
        return (object) [
            'entity' => $this->currentEntity,
            'id' => $this->currentId,
            'entityNameSingular' => $this->currentEntity ? rtrim(ucfirst($this->currentEntity), 's') : null,
        ];
    }

    /**
     * Comprueba si una ruta de navegación principal está activa.
     * Ayuda a resaltar el enlace de la sección actual en la barra lateral.
     *
     * @param string $entityPath La ruta a comprobar (ej. '/profiles', '/incomes').
     * @return bool True si la ruta coincide con la entidad actual, false en caso contrario.
     */
    public function isActive(string $entityPath): bool
    {
        $basePath = explode('/', trim($entityPath, '/'))[0];
        return $this->currentEntity === $basePath;
    }
}