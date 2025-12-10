# 🍽️ Sistema de Gestión de Restaurante

Sistema integral desarrollado en **PHP** y **MySQL** para automatizar el flujo de trabajo en un restaurante. Permite gestionar roles, comandas en tiempo real, facturación y reportes.

## 🚀 Características Principales

* **👥 Gestión de Roles:** Módulos separados para Administrador, Cajeros, Meseros y Cocina.
* **📋 Control de Comandas:** Toma de pedidos en mesas y seguimiento de estado (En espera/Entregado).
* **💰 Facturación y Caja:** División de cuentas, métodos de pago y cierre de caja.
* **📄 Reportes PDF:** Generación de reportes de ventas y menús utilizando la librería **TCPDF**.
* **🍔 Gestión de Menú:** Administración de platos, precios y disponibilidad por día.

## 🛠️ Tecnologías

* **Lenguaje:** PHP 8+
* **Base de Datos:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript (AJAX para el carrito)
* **Librerías:** TCPDF (vía Composer)

## 📦 Instalación y Puesta en Marcha

Sigue estos pasos para probar el proyecto en tu entorno local (XAMPP/WAMP):

1.  **Clonar el repositorio**
    ```bash
    git clone [https://github.com/TU_USUARIO/NOMBRE_DEL_REPO.git](https://github.com/TU_USUARIO/NOMBRE_DEL_REPO.git)
    ```

2.  **Base de Datos**
    * Abre phpMyAdmin y crea una base de datos llamada `restaurantephp`.
    * Importa el archivo SQL ubicado en: `/database/restaurantephp.sql`.

3.  **Configuración**
    * Ve a la carpeta `/php/`.
    * Renombra el archivo `conexion.example.php` a `conexion.php`.
    * Edita `conexion.php` y pon tus credenciales (usualmente user: `root`, pass: vacío).

4.  **Dependencias**
    * Abre la terminal en la carpeta del proyecto y ejecuta:
    ```bash
    composer install
    ```
    *(Esto descargará la librería TCPDF necesaria para los reportes).*

## 🔑 Credenciales de Acceso (Demo)

Puedes usar estos usuarios precargados para probar los diferentes roles:

| Rol | Usuario | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin` | `12345` |
| **Mesero** | `mesero` | `12345` |

---
*Desarrollado con ❤️ para optimizar la gestión gastronómica.*