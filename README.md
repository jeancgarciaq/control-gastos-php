# Control de Gastos en PHP

Una aplicación web sencilla para el control de gastos personales, desarrollada en PHP puro siguiendo el patrón de arquitectura Modelo-Vista-Controlador (MVC). Este proyecto sirve como demostración del uso de PHP moderno, PDO para la interacción con la base de datos, y un sistema de enrutamiento propio.

## ✨ Características

*   **Autenticación de Usuarios:** Registro e inicio de sesión seguros.
*   **Gestión de Perfil:** Los usuarios pueden ver y actualizar la información de su perfil.
*   **Gestión de Ingresos:** Funcionalidad CRUD (Crear, Leer, Actualizar, Eliminar) para los ingresos.
*   **Gestión de Gastos:** Funcionalidad CRUD (Crear, Leer, Actualizar, Eliminar) para los gastos.
*   **Dashboard:** Una vista principal que resume la información financiera del usuario.
*   **Enrutamiento Limpio:** URLs amigables gracias a un enrutador personalizado.
*   **Seguridad:** Uso de variables de entorno, contraseñas hasheadas y protección básica contra ataques comunes.
*   **Diseño Responsivo:** Interfaz de usuario creada con Tailwind CSS que se adapta a dispositivos móviles y de escritorio.

## 🛠️ Tecnologías Utilizadas

*   **Backend:** PHP 8+
*   **Base de Datos:** MySQL (con PDO para la conexión)
*   **Frontend:**
    *   HTML5
    *   CSS3 con [Tailwind CSS](https://tailwindcss.com/)
    *   JavaScript
*   **Seguridad:** Google reCAPTCHA v3

## 🚀 Instalación

Sigue estos pasos para configurar el proyecto en tu entorno de desarrollo local.

### Prerrequisitos

*   PHP 8.0 o superior
*   Servidor web (Apache, Nginx, o el servidor integrado de PHP)
*   MySQL o MariaDB
*   Composer (para futuras dependencias)

### Pasos

1.  **Clona el repositorio:**
    ```bash
    git clone https://github.com/jeancgarciaq/control-gastos-php.git
    cd control-gastos-php
    ```

2.  **Configura las variables de entorno:**
    *   Crea una copia del archivo de ejemplo `.env-example` y renómbrala a `.env`.
        ```bash
        cp .env-example .env
        ```
    *   Abre el archivo `.env` y modifica los valores para que coincidan con tu configuración local, especialmente los de la base de datos (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

3.  **Configura la base de datos:**
    *   Crea una base de datos en MySQL con el nombre que especificaste en el archivo `.env`.
    *   Importa la estructura de la base de datos. Puedes usar un archivo `.sql` si lo tienes, o crear las tablas manualmente según la estructura requerida por los modelos. *(Nota: Sería ideal añadir un archivo `database.sql` al repositorio con la estructura inicial).*

4.  **Compila los estilos de Tailwind CSS (Opcional):**
    Si realizas cambios en las clases de Tailwind en los archivos `.php` o `input.css`, necesitarás recompilar el CSS.
    ```bash
    npx tailwindcss -i ./public/css/input.css -o ./public/output.css --watch
    ```

## ▶️ Uso

1.  **Inicia el servidor web:**
    Puedes usar el servidor de desarrollo integrado de PHP, que es ideal para desarrollo local. Desde la carpeta raíz del proyecto, ejecuta:
    ```bash
    php -S localhost:8000 -t public
    ```
    El flag `-t public` es muy importante, ya que asegura que todas las peticiones se dirijan al `index.php` dentro de la carpeta `public`, que es el punto de entrada de la aplicación.

2.  **Accede a la aplicación:**
    Abre tu navegador y visita `http://localhost:8000`.

## 📁 Estructura del Proyecto

El proyecto sigue una estructura MVC para mantener el código organizado y escalable.

```
.
├── app/
│   ├── Controllers/  # Controladores que manejan la lógica de la aplicación
│   ├── Core/         # Clases del núcleo (Auth, Router, etc.)
│   ├── Models/       # Modelos que interactúan con la base de datos
│   ├── Views/        # Vistas (archivos .php con HTML)
│   └── routes.php    # Definición de todas las rutas de la aplicación
├── public/           # Carpeta pública (punto de entrada)
│   ├── css/
│   ├── js/
│   ├── index.php     # Punto de entrada de todas las peticiones
│   └── output.css    # Archivo CSS compilado por Tailwind
├── .env.example      # Ejemplo de archivo de configuración
├── composer.json     # Dependencias de PHP (si las hubiera)
├── tailwind.config.js # Configuración de Tailwind CSS
└── README.md
```

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Si deseas mejorar este proyecto, por favor sigue estos pasos:

1.  Haz un "Fork" del repositorio.
2.  Crea una nueva rama (`git checkout -b feature/nueva-funcionalidad`).
3.  Realiza tus cambios y haz "commit" (`git commit -am 'Añade nueva funcionalidad'`).
4.  Haz "push" a la rama (`git push origin feature/nueva-funcionalidad`).
5.  Abre un "Pull Request".

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.
