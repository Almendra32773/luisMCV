# Mini MVC PHP

![PHP](https://img.shields.io/badge/PHP-7.1+-blue)
![RedBeanPHP](https://img.shields.io/badge/RedBeanPHP-5.7-orange)
![MiligramCSS](https://img.shields.io/badge/Miligram-1.4.1-yellowgreen)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-blueviolet)

Un **framework MVC mínimo en PHP** con autenticación, CSRF y vistas dinámicas usando **RedBeanPHP**, **MiligramCSS** y **Alpine.js**.

---

## Características

- MVC básico: Controllers, Models, Views con layouts y secciones  
- Autenticación: Login, registro y logout con `$_SESSION` y `password_hash`  
- Middleware: `auth()` y `guest()` para proteger rutas  
- CSRF: Protección de formularios  
- Mensajes flash: Tostadas (Alpine.js) para feedback del usuario  
- Minimalista: MiligramCSS + Alpine.js, sin Node.js ni compiladores  

---

## Requisitos

- PHP 7.1+  
- Composer  
- MySQL  
- Extensión PDO MySQL habilitada  

---

## Instalación

1. Clonar el repositorio:

    ```bash
        git clone https://github.com/josephmora1810/myMVC.git
        cd myMVC
    ```

2. Instalar dependencias con Composer:

    ```bash
    composer install
    ```

3. Crear la base de datos y la tabla `users`:

    ```sql
    CREATE DATABASE mi_mvc_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

    CREATE TABLE users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    ```

4. Configurar archivo `.env`:

    ```
    APP_NAME="Mi MVC PHP"
    APP_ENV=development
    APP_URL=http://mvc.test

    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=mi_mvc_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. Iniciar servidor:

    ```bash
    php -S localhost:8000 -t public
    ```

> Ahora puedes acceder a tu app en `http://localhost:8000`.

---

## Uso

- Rutas principales:

    | Ruta        | Método | Controlador       | Descripción                |
    |------------ |--------|-----------------|----------------------------|
    | /           | GET    | HomeController   | Página de inicio           |
    | /login      | GET    | AuthController   | Formulario de login        |
    | /login      | POST   | AuthController   | Autenticar usuario         |
    | /register   | GET    | AuthController   | Formulario de registro     |
    | /register   | POST   | AuthController   | Crear nuevo usuario        |
    | /logout     | POST   | AuthController   | Cerrar sesión              |
    | /users/show | GET    | UsersController  | Ver detalles de un usuario |

- Para rutas protegidas se utiliza `Middleware::auth()` y para guests `Middleware::guest()`.

---

## Tecnologías

- PHP 7.1+  
- RedBeanPHP para ORM simple  
- MiligramCSS para estilos minimalistas  
- Alpine.js para interactividad ligera  
- CSRF token y mensajes flash con `$_SESSION`  

---

## Estructura del proyecto

## 📂 Estructura del Directorio

```text
proyecto/
├── app/                  # Lógica central de la aplicación
│   ├── Controllers/      # Controladores (manejan las peticiones)
│   ├── Models/           # Modelos (interacción con la base de datos)
│   └── Views/            # Archivos de plantilla y frontend
│       ├── layouts/      # Estructuras base (Header, Footer, etc.)
│       ├── partials/     # Componentes reutilizables
│       ├── auth/         # Vistas de autenticación (Login, Registro)
│       └── users/        # Vistas de gestión de usuarios
├── core/                 # El "Motor" del sistema (Framework Core)
│   ├── App.php           # Clase principal de inicialización
│   ├── Controller.php    # Clase base de la que heredan los controladores
│   ├── Model.php         # Clase base para la conexión a datos
│   ├── View.php          # Motor de renderizado de vistas
│   ├── Router.php        # Manejador de rutas y URLs
│   ├── Middleware.php    # Filtros de peticiones (Seguridad)
│   └── Csrf.php          # Protección contra ataques CSRF
├── public/               # Directorio raíz público (Assets)
│   ├── css/              # Hojas de estilo
│   ├── js/               # Archivos JavaScript
│   └── images/           # Recursos gráficos
├── vendor/               # Dependencias de Composer (Autoload)
├── .env                  # Variables de entorno (Configuración sensible)
└── composer.json         # Configuración de dependencias y scripts

```
---

## Notas

- Todos los formularios POST incluyen protección CSRF  
- Las contraseñas se almacenan hasheadas con `password_hash()`  
- Mensajes de feedback se muestran con tostadas de Alpine.js  
- Layout base y secciones permiten reusar header/footer y navbar
